<?php

declare(strict_types=1);

namespace UIX\UI;

/**
 * Custom post type registration.
 *
 * Config keys match WP register_post_type() args with sensible defaults.
 * Also accepts a 'metaboxes' array of Metabox slugs to attach.
 */
class PostType extends Node
{
    /** @var array<string, Metabox> */
    private array $metaboxes = [];

    public function __construct(string $slug, array $config = [])
    {
        parent::__construct($slug, 'post_type', $config, null);
    }

    // ------------------------------------------------------------------
    // Fluent helpers
    // ------------------------------------------------------------------

    public function metabox(string $slug, array $config = []): Metabox
    {
        $config['screen'] = $this->slug;
        $metabox = new Metabox($slug, $config);
        $this->metaboxes[$slug] = $metabox;
        return $metabox;
    }

    /** @return array<string, Metabox> */
    public function getMetaboxes(): array
    {
        return $this->metaboxes;
    }

    // ------------------------------------------------------------------
    // WordPress integration
    // ------------------------------------------------------------------

    public function registerWithWordPress(): void
    {
        $defaults = [
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'has_archive'        => false,
            'supports'           => ['title', 'editor', 'thumbnail'],
            'rewrite'            => ['slug' => $this->slug],
            'labels'             => $this->buildLabels(),
        ];

        register_post_type($this->slug, array_merge($defaults, $this->config));
    }

    /** @return array<string, string> */
    private function buildLabels(): array
    {
        $singular = $this->cfg('singular', ucfirst(str_replace(['-', '_'], ' ', $this->slug)));
        $plural   = $this->cfg('plural', $singular . 's');

        return [
            'name'               => $plural,
            'singular_name'      => $singular,
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New ' . $singular,
            'edit_item'          => 'Edit ' . $singular,
            'new_item'           => 'New ' . $singular,
            'view_item'          => 'View ' . $singular,
            'search_items'       => 'Search ' . $plural,
            'not_found'          => 'No ' . strtolower($plural) . ' found.',
            'not_found_in_trash' => 'No ' . strtolower($plural) . ' found in Trash.',
        ];
    }

    // ------------------------------------------------------------------
    // Schema
    // ------------------------------------------------------------------

    protected function extraSchema(): array
    {
        return [
            'singular' => $this->cfg('singular', ''),
            'plural'   => $this->cfg('plural', ''),
        ];
    }
}
