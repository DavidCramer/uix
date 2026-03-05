<?php

declare(strict_types=1);

namespace UIX\UI;

use UIX\UI\Control\Control;
use UIX\UI\Control\Type;

/**
 * A labelled group of controls within a Panel or Page.
 *
 * Fluent control factory methods delegate to Control::make() and attach the
 * new control as a child.  Each method returns $this so chains stay readable:
 *
 *   ->section('general', ['title' => 'General'])
 *       ->text('name',    ['label' => 'Name'])
 *       ->toggle('debug', ['label' => 'Debug'])
 *   ->end()
 */
class Section extends Node
{
    public function __construct(string $slug, array $config = [], ?Node $parent = null)
    {
        parent::__construct($slug, 'section', $config, $parent);
    }

    // ------------------------------------------------------------------
    // Fluent control factories
    // ------------------------------------------------------------------

    private function control(string $slug, Type $controlType, array $config = []): static
    {
        $config['controlType'] = $controlType->value;
        $this->addChild(new Control($slug, $config, $this));
        return $this;
    }

    public function text(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Text, $config);
    }

    public function textarea(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Textarea, $config);
    }

    public function email(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Email, $config);
    }

    public function number(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Number, $config);
    }

    public function hidden(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Hidden, $config);
    }

    public function checkbox(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Checkbox, $config);
    }

    public function radio(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Radio, $config);
    }

    public function select(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Select, $config);
    }

    public function color(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Color, $config);
    }

    public function date(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Date, $config);
    }

    public function image(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Image, $config);
    }

    public function file(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::File, $config);
    }

    public function editor(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Editor, $config);
    }

    public function toggle(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Toggle, $config);
    }

    public function slider(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Slider, $config);
    }

    public function autocomplete(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Autocomplete, $config);
    }

    public function button(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Button, $config);
    }

    public function separator(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Separator, $config);
    }

    public function template(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::Template, $config);
    }

    public function postRelation(string $slug, array $config = []): static
    {
        return $this->control($slug, Type::PostRelation, $config);
    }

    // ------------------------------------------------------------------
    // Nested repeater
    // ------------------------------------------------------------------

    public function repeat(string $slug, array $config = []): Repeat
    {
        $repeat = new Repeat($slug, $config, $this);
        $this->addChild($repeat);
        return $repeat;
    }

    // ------------------------------------------------------------------
    // Navigation back up the tree
    // ------------------------------------------------------------------

    /**
     * Return the parent Panel or Page so the fluent chain can continue upward.
     */
    public function end(): Panel|Page|Metabox|PostType|null
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
            'description' => $this->cfg('description', ''),
        ];
    }
}
