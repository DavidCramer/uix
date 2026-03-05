<?php

declare(strict_types=1);

namespace UIX\UI;

/**
 * A tabbed or collapsible container holding one or more Sections.
 *
 * Panels are optional — you can attach sections directly to a Page or Metabox.
 */
class Panel extends Node
{
    public function __construct(string $slug, array $config = [], ?Node $parent = null)
    {
        parent::__construct($slug, 'panel', $config, $parent);
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

    public function end(): Page|Metabox|null
    {
        return $this->parent; // @phpstan-ignore-line
    }

    // ------------------------------------------------------------------
    // Schema
    // ------------------------------------------------------------------

    protected function extraSchema(): array
    {
        return [
            'title'       => $this->cfg('title', ''),
            'icon'        => $this->cfg('icon', ''),
            'collapsible' => $this->cfg('collapsible', false),
            'defaultOpen' => $this->cfg('defaultOpen', true),
        ];
    }
}
