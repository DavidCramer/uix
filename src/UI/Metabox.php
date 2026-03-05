<?php

declare(strict_types=1);

namespace UIX\UI;

use UIX\Data\DataSourceInterface;
use UIX\Data\PostMetaSource;

/**
 * A post editor metabox.
 *
 * Config keys:
 *   name        string  Metabox title (required)
 *   screen      string|array  Post type(s) to show on (default: null = all)
 *   context     string  'normal' | 'side' | 'advanced' (default: advanced)
 *   priority    string  'default' | 'high' | 'low' (default: default)
 *   store_key   string  post_meta key (default: uix_{slug})
 */
class Metabox extends Node
{
    private bool $isActiveFlag = false;
    private DataSourceInterface $dataSource;

    public function __construct(string $slug, array $config = [])
    {
        parent::__construct($slug, 'metabox', $config, null);
        $this->dataSource = new PostMetaSource();
    }

    // ------------------------------------------------------------------
    // Fluent child factories
    // ------------------------------------------------------------------

    public function section(string $slug, array $config = []): Section
    {
        $section = new Section($slug, $config, $this);
        $this->addChild($section);
        return $section;
    }

    public function panel(string $slug, array $config = []): Panel
    {
        $panel = new Panel($slug, $config, $this);
        $this->addChild($panel);
        return $panel;
    }

    public function dataSource(DataSourceInterface $source): static
    {
        $this->dataSource = $source;
        return $this;
    }

    // ------------------------------------------------------------------
    // WordPress integration
    // ------------------------------------------------------------------

    public function registerWithWordPress(): void
    {
        if (empty($this->cfg('name'))) {
            return;
        }

        $screen   = $this->cfg('screen');
        $context  = $this->cfg('context', 'advanced');
        $priority = $this->cfg('priority', 'default');

        // Track active state via current_screen.
        add_action('current_screen', function (\WP_Screen $s) use ($screen): void {
            if ($s->base === 'post' && (null === $screen || in_array($s->id, (array) $screen, true))) {
                $this->isActiveFlag = true;
            }
        }, 25);

        add_meta_box(
            'uix-metabox-' . $this->id(),
            esc_html($this->cfg('name', '')),
            [$this, 'renderMetabox'],
            $screen,
            $context,
            $priority,
        );
    }

    public function renderMetabox(\WP_Post $post): void
    {
        printf(
            '<div id="uix-metabox-%s" class="uix-metabox-root" data-post-id="%d"></div>',
            esc_attr($this->slug),
            $post->ID,
        );
    }

    public function enqueueAssets(): void
    {
        $buildUrl = plugin_dir_url(UIX_PLUGIN_FILE) . 'build/';

        wp_enqueue_script(
            'uix-classic',
            $buildUrl . 'classic.js',
            ['wp-element', 'wp-data', 'wp-api-fetch', 'wp-components', 'wp-i18n'],
            UIX_VERSION,
            true,
        );

        wp_enqueue_style(
            'uix-classic',
            $buildUrl . 'classic.css',
            ['wp-components'],
            UIX_VERSION,
        );

        wp_localize_script('uix-classic', 'uixMetaboxConfigs', $this->buildLocalizedConfig());
    }

    /** @return array<string, mixed> */
    private function buildLocalizedConfig(): array
    {
        $existing = wp_scripts()->get_data('uix-classic', 'data');
        $boxes    = $existing ? json_decode(
            (string) preg_replace('/^var uixMetaboxConfigs\s*=\s*/', '', rtrim((string) $existing, ';')),
            true,
        ) ?? [] : [];

        $boxes[$this->slug] = $this->toSchema();
        return $boxes;
    }

    public function savePost(int $postId, \WP_Post $post): void
    {
        // Nonce validation happens in the REST controller — classic saves go
        // through the REST API from the React side, so this is a no-op for now.
        // Direct WP save_post fallback can be wired here if needed.
    }

    // ------------------------------------------------------------------
    // Activation
    // ------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->isActiveFlag;
    }

    // ------------------------------------------------------------------
    // Schema
    // ------------------------------------------------------------------

    protected function extraSchema(): array
    {
        $storeKey = $this->cfg('store_key', 'uix_' . $this->slug);
        return [
            'name'     => $this->cfg('name', ''),
            'context'  => $this->cfg('context', 'advanced'),
            'priority' => $this->cfg('priority', 'default'),
            'color'    => $this->cfg('color', '#0073aa'),
            'data'     => [
                'source'   => $this->dataSource->type(),
                'storeKey' => $storeKey,
                'nonce'    => wp_create_nonce('uix_rest'),
                'restBase' => rest_url('uix/v1'),
            ],
        ];
    }
}
