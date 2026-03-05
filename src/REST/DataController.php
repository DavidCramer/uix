<?php

declare(strict_types=1);

namespace UIX\REST;

use UIX\Data\OptionsSource;
use UIX\Data\PostMetaSource;
use UIX\UIX;

/**
 * REST API controller for loading and saving UIX field data.
 *
 * Routes:
 *   GET  /wp-json/uix/v1/data/{id}               — load options-backed data
 *   POST /wp-json/uix/v1/data/{id}               — save options-backed data
 *   GET  /wp-json/uix/v1/data/{id}/{post_id}     — load post-meta-backed data
 *   POST /wp-json/uix/v1/data/{id}/{post_id}     — save post-meta-backed data
 */
class DataController
{
    public static function register(): void
    {
        register_rest_route('uix/v1', '/data/(?P<id>[\w-]+)', [
            [
                'methods'             => 'GET',
                'callback'            => [self::class, 'load'],
                'permission_callback' => [self::class, 'canAccess'],
                'args'                => self::idArgs(),
            ],
            [
                'methods'             => 'POST',
                'callback'            => [self::class, 'save'],
                'permission_callback' => [self::class, 'canAccess'],
                'args'                => self::idArgs(),
            ],
        ]);

        register_rest_route('uix/v1', '/data/(?P<id>[\w-]+)/(?P<post_id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [self::class, 'load'],
                'permission_callback' => [self::class, 'canAccessPost'],
                'args'                => self::postArgs(),
            ],
            [
                'methods'             => 'POST',
                'callback'            => [self::class, 'save'],
                'permission_callback' => [self::class, 'canAccessPost'],
                'args'                => self::postArgs(),
            ],
        ]);
    }

    // ------------------------------------------------------------------
    // Handlers
    // ------------------------------------------------------------------

    public static function load(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $id     = $request->get_param('id');
        $postId = (int) ($request->get_param('post_id') ?? 0) ?: null;

        [$source, $storeKey] = self::resolveSource($id, $postId);
        if ($source === null) {
            return new \WP_Error('uix_not_found', 'UIX node not found.', ['status' => 404]);
        }

        $data = $source->load($storeKey, $postId);
        return rest_ensure_response(['data' => $data]);
    }

    public static function save(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $id     = $request->get_param('id');
        $postId = (int) ($request->get_param('post_id') ?? 0) ?: null;

        [$source, $storeKey] = self::resolveSource($id, $postId);
        if ($source === null) {
            return new \WP_Error('uix_not_found', 'UIX node not found.', ['status' => 404]);
        }

        $body = $request->get_json_params();
        if (! is_array($body)) {
            return new \WP_Error('uix_bad_data', 'Request body must be a JSON object.', ['status' => 400]);
        }

        $sanitized = self::sanitizeData($body);
        $source->save($storeKey, $sanitized, $postId);

        do_action('uix_data_saved', $id, $sanitized, $postId);

        return rest_ensure_response(['success' => true, 'data' => $sanitized]);
    }

    // ------------------------------------------------------------------
    // Permissions
    // ------------------------------------------------------------------

    public static function canAccess(\WP_REST_Request $request): bool|\WP_Error
    {
        if (! wp_verify_nonce($request->get_header('X-WP-Nonce') ?? '', 'wp_rest')) {
            return new \WP_Error('uix_forbidden', 'Invalid nonce.', ['status' => 403]);
        }
        return current_user_can('edit_posts');
    }

    public static function canAccessPost(\WP_REST_Request $request): bool|\WP_Error
    {
        if (! wp_verify_nonce($request->get_header('X-WP-Nonce') ?? '', 'wp_rest')) {
            return new \WP_Error('uix_forbidden', 'Invalid nonce.', ['status' => 403]);
        }
        $postId = (int) $request->get_param('post_id');
        return current_user_can('edit_post', $postId);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * @return array{0: \UIX\Contracts\DataSourceInterface|null, 1: string}
     */
    private static function resolveSource(string $id, ?int $postId): array
    {
        // Try pages first.
        foreach (UIX::getPages() as $page) {
            if ($page->slug() === $id) {
                $storeKey = $page->toSchema()['data']['storeKey'] ?? ('uix_' . $id);
                return [new OptionsSource(), $storeKey];
            }
        }

        // Try metaboxes.
        foreach (UIX::getMetaboxes() as $metabox) {
            if ($metabox->slug() === $id) {
                $storeKey = $metabox->toSchema()['data']['storeKey'] ?? ('uix_' . $id);
                return [new PostMetaSource(), $storeKey];
            }
        }

        return [null, ''];
    }

    /** @param array<string, mixed> $data */
    private static function sanitizeData(array $data): array
    {
        $clean = [];
        array_walk_recursive($data, function (mixed &$value): void {
            if (is_string($value)) {
                $value = wp_kses_post($value);
            }
        });
        return $data;
    }

    /** @return array<string, mixed> */
    private static function idArgs(): array
    {
        return [
            'id' => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_key',
            ],
        ];
    }

    /** @return array<string, mixed> */
    private static function postArgs(): array
    {
        return array_merge(self::idArgs(), [
            'post_id' => [
                'required'          => true,
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
            ],
        ]);
    }
}
