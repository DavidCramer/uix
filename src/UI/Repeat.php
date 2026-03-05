<?php

declare(strict_types=1);

namespace UIX\UI;

use UIX\UI\Control\Control;
use UIX\UI\Control\Type;

/**
 * A repeatable row of controls.  Each row uses the same schema — the frontend
 * renders N instances based on saved data.
 *
 * Usage:
 *   ->repeat('gallery_items', ['label' => 'Gallery Items', 'max' => 10])
 *       ->image('image',       ['label' => 'Image'])
 *       ->text('caption',      ['label' => 'Caption'])
 *   ->end()
 */
class Repeat extends Node
{
    public function __construct(string $slug, array $config = [], ?Node $parent = null)
    {
        parent::__construct($slug, 'repeat', $config, $parent);
    }

    // ------------------------------------------------------------------
    // Fluent control factories (mirrors Section)
    // ------------------------------------------------------------------

    private function control(string $slug, Type $controlType, array $config = []): static
    {
        $config['controlType'] = $controlType->value;
        $this->addChild(new Control($slug, $config, $this));
        return $this;
    }

    public function text(string $slug, array $config = []): static         { return $this->control($slug, Type::Text, $config); }
    public function textarea(string $slug, array $config = []): static     { return $this->control($slug, Type::Textarea, $config); }
    public function email(string $slug, array $config = []): static        { return $this->control($slug, Type::Email, $config); }
    public function number(string $slug, array $config = []): static       { return $this->control($slug, Type::Number, $config); }
    public function hidden(string $slug, array $config = []): static       { return $this->control($slug, Type::Hidden, $config); }
    public function checkbox(string $slug, array $config = []): static     { return $this->control($slug, Type::Checkbox, $config); }
    public function radio(string $slug, array $config = []): static        { return $this->control($slug, Type::Radio, $config); }
    public function select(string $slug, array $config = []): static       { return $this->control($slug, Type::Select, $config); }
    public function color(string $slug, array $config = []): static        { return $this->control($slug, Type::Color, $config); }
    public function date(string $slug, array $config = []): static         { return $this->control($slug, Type::Date, $config); }
    public function image(string $slug, array $config = []): static        { return $this->control($slug, Type::Image, $config); }
    public function file(string $slug, array $config = []): static         { return $this->control($slug, Type::File, $config); }
    public function editor(string $slug, array $config = []): static       { return $this->control($slug, Type::Editor, $config); }
    public function toggle(string $slug, array $config = []): static       { return $this->control($slug, Type::Toggle, $config); }
    public function slider(string $slug, array $config = []): static       { return $this->control($slug, Type::Slider, $config); }
    public function autocomplete(string $slug, array $config = []): static { return $this->control($slug, Type::Autocomplete, $config); }
    public function button(string $slug, array $config = []): static       { return $this->control($slug, Type::Button, $config); }
    public function separator(string $slug, array $config = []): static    { return $this->control($slug, Type::Separator, $config); }
    public function postRelation(string $slug, array $config = []): static { return $this->control($slug, Type::PostRelation, $config); }

    public function end(): Section|Panel|null
    {
        return $this->parent; // @phpstan-ignore-line
    }

    // ------------------------------------------------------------------
    // Schema
    // ------------------------------------------------------------------

    protected function extraSchema(): array
    {
        return [
            'label'   => $this->cfg('label', ''),
            'min'     => $this->cfg('min', 0),
            'max'     => $this->cfg('max', null),
            'addLabel'    => $this->cfg('addLabel', 'Add Item'),
            'removeLabel' => $this->cfg('removeLabel', 'Remove'),
        ];
    }
}
