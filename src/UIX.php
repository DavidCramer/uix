<?php

declare(strict_types=1);

namespace UIX;

use UIX\UI\Metabox;
use UIX\UI\Page;
use UIX\UI\PostType;

/**
 * UIX — the single entry point for registering UI.
 *
 * Usage:
 *
 *   UIX::page('my-settings', ['title' => 'My Settings', ...])
 *       ->section('general', ['title' => 'General'])
 *           ->text('site_name',    ['label' => 'Site Name'])
 *           ->toggle('debug_mode', ['label' => 'Debug Mode'])
 *       ->end();
 *
 *   UIX::metabox('my-meta', ['name' => 'My Metabox', 'screen' => 'post'])
 *       ->section('fields', ['title' => 'Fields'])
 *           ->text('custom_field', ['label' => 'Custom Field'])
 *       ->end();
 */
final class UIX
{
    private static ?self $instance = null;

    /** @var array<string, Page> */
    private array $pages = [];

    /** @var array<string, Metabox> */
    private array $metaboxes = [];

    /** @var array<string, PostType> */
    private array $postTypes = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ------------------------------------------------------------------
    // Factory methods
    // ------------------------------------------------------------------

    /**
     * Register a top-level admin page (or sub-page).
     */
    public static function page(string $slug, array $config = []): Page
    {
        $page = new Page($slug, $config);
        self::getInstance()->pages[$slug] = $page;
        return $page;
    }

    /**
     * Register a metabox.
     */
    public static function metabox(string $slug, array $config = []): Metabox
    {
        $metabox = new Metabox($slug, $config);
        self::getInstance()->metaboxes[$slug] = $metabox;
        return $metabox;
    }

    /**
     * Register a custom post type.
     */
    public static function postType(string $slug, array $config = []): PostType
    {
        $postType = new PostType($slug, $config);
        self::getInstance()->postTypes[$slug] = $postType;
        return $postType;
    }

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------

    /** @return array<string, Page> */
    public static function getPages(): array
    {
        return self::getInstance()->pages;
    }

    /** @return array<string, Metabox> */
    public static function getMetaboxes(): array
    {
        return self::getInstance()->metaboxes;
    }

    /** @return array<string, PostType> */
    public static function getPostTypes(): array
    {
        return self::getInstance()->postTypes;
    }

    /**
     * Bootstrap all registered UI: hook into WordPress.
     * Called once on `plugins_loaded` from uix-bootstrap.php.
     */
    public static function boot(): void
    {
        $instance = self::getInstance();

        // Register REST API routes.
        add_action('rest_api_init', [REST\DataController::class, 'register']);

        // Register post types.
        add_action('init', function () use ($instance): void {
            foreach ($instance->postTypes as $postType) {
                $postType->registerWithWordPress();
            }
        });

        // Register admin pages.
        add_action('admin_menu', function () use ($instance): void {
            foreach ($instance->pages as $page) {
                $page->registerWithWordPress();
            }
        }, 9);

        // Register metaboxes.
        add_action('add_meta_boxes', function () use ($instance): void {
            foreach ($instance->metaboxes as $metabox) {
                $metabox->registerWithWordPress();
            }
        }, 9);

        // Save metabox data on post save.
        add_action('save_post', function (int $postId, \WP_Post $post) use ($instance): void {
            foreach ($instance->metaboxes as $metabox) {
                if ($metabox->isActive()) {
                    $metabox->savePost($postId, $post);
                }
            }
        }, 10, 2);

        // Enqueue assets for pages/metaboxes.
        add_action('admin_enqueue_scripts', function (string $hook) use ($instance): void {
            foreach ($instance->pages as $page) {
                if ($page->isActive()) {
                    $page->enqueueAssets();
                }
            }
            foreach ($instance->metaboxes as $metabox) {
                if ($metabox->isActive()) {
                    $metabox->enqueueAssets();
                }
            }
        });
    }
}
