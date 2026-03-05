<?php

declare(strict_types=1);

namespace UIX\UI;

use UIX\Data\DataSourceInterface;
use UIX\Data\OptionsSource;

/**
 * An admin settings page (top-level or sub-page).
 *
 * Config keys:
 *   title       string  Page <title> / menu label
 *   menu_title  string  Sidebar menu label (falls back to title)
 *   capability  string  WP capability required (default: manage_options)
 *   icon        string  Dashicon or base64 SVG (default: dashicons-admin-generic)
 *   position    int     Menu position (default: null = auto)
 *   parent      string  Parent slug for sub-pages (omit for top-level)
 *   color       string  Accent hex color shown in the UI header
 *   data_source string  'options' (default) — expandable via filter
 *   store_key   string  WP option name (default: uix_{slug})
 */
class Page extends Node
{
    private string $screenHookSuffix = '';
    private DataSourceInterface $dataSource;

    public function __construct(string $slug, array $config = [])
    {
        parent::__construct($slug, 'page', $config, null);
        $this->dataSource = new OptionsSource();
    }

    // ------------------------------------------------------------------
    // Fluent child factories
    // ------------------------------------------------------------------

    public function panel(string $slug, array $config = []): Panel
    {
        $panel = new Panel($slug, $config, $this);
        $this->addChild($panel);
        return $panel;
    }

    public function section(string $slug, array $config = []): Section
    {
        $section = new Section($slug, $config, $this);
        $this->addChild($section);
        return $section;
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
        if (empty($this->cfg('title'))) {
            return;
        }

        $title     = $this->cfg('title', '');
        $menuTitle = $this->cfg('menu_title', $title);
        $cap       = $this->cfg('capability', 'manage_options');
        $icon      = $this->cfg('icon', 'dashicons-admin-generic');
        $position  = $this->cfg('position');
        $parent    = $this->cfg('parent');

        if ($parent) {
            $this->screenHookSuffix = (string) add_submenu_page(
                $parent,
                $title,
                $menuTitle,
                $cap,
                $this->slug,
                [$this, 'renderPage'],
            );
        } else {
            $this->screenHookSuffix = (string) add_menu_page(
                $title,
                $menuTitle,
                $cap,
                $this->slug,
                [$this, 'renderPage'],
                $icon,
                $position,
            );
        }
    }

    public function renderPage(): void
    {
        echo '<div id="uix-page-' . esc_attr($this->slug) . '" class="uix-page-root"></div>';
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

        // Pass schema + REST config to React.
        wp_localize_script('uix-classic', 'uixPageConfigs', $this->buildLocalizedConfig());
    }

    /** @return array<string, mixed> */
    private function buildLocalizedConfig(): array
    {
        // Merge with any existing pages already localized this request.
        $existing = wp_scripts()->get_data('uix-classic', 'data');
        $pages    = $existing ? json_decode(
            (string) preg_replace('/^var uixPageConfigs\s*=\s*/', '', rtrim((string) $existing, ';')),
            true,
        ) ?? [] : [];

        $pages[$this->slug] = $this->toSchema();
        return $pages;
    }

    // ------------------------------------------------------------------
    // Activation
    // ------------------------------------------------------------------

    public function isActive(): bool
    {
        if (! is_admin()) {
            return false;
        }
        $screen = get_current_screen();
        return $screen && $screen->base === $this->screenHookSuffix;
    }

    // ------------------------------------------------------------------
    // Schema
    // ------------------------------------------------------------------

    protected function extraSchema(): array
    {
        $storeKey = $this->cfg('store_key', 'uix_' . $this->slug);
        return [
            'title'      => $this->cfg('title', ''),
            'capability' => $this->cfg('capability', 'manage_options'),
            'color'      => $this->cfg('color', '#0073aa'),
            'data'       => [
                'source'   => $this->dataSource->type(),
                'storeKey' => $storeKey,
                'nonce'    => wp_create_nonce('uix_rest'),
                'restBase' => rest_url('uix/v1'),
            ],
        ];
    }
}
