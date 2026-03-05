<?php

declare(strict_types=1);

namespace UIX\UI\Control;

use UIX\UI\Node;

/**
 * A single form control (leaf node).
 *
 * All type-specific behaviour is handled by the React frontend; PHP only
 * needs to emit the right schema keys.
 *
 * Common config keys (all optional unless noted):
 *   controlType  string   One of the Type enum values — set by Section/Repeat factories
 *   label        string   Field label
 *   description  string   Help text shown below the control
 *   placeholder  string   Input placeholder
 *   default      mixed    Default value
 *   required     bool     Marks field as required (frontend validation)
 *   readonly     bool     Renders as read-only
 *   disabled     bool     Renders as disabled
 *   validation   array    Validation rules: ['min' => 1, 'max' => 255, 'pattern' => '...']
 *   options      array    For select/radio: [['value' => 'x', 'label' => 'X'], ...]
 *   min          int      For number/slider
 *   max          int      For number/slider
 *   step         float    For number/slider
 *   rows         int      For textarea
 *   multiple     bool     For select (multi-select)
 *   mime_types   array    For image/file: accepted MIME types
 *   post_type    string   For post_relation
 *   sanitize     string   Server-side sanitization callback name (optional)
 */
class Control extends Node
{
    public function __construct(string $slug, array $config = [], ?Node $parent = null)
    {
        parent::__construct($slug, 'control', $config, $parent);
    }

    protected function extraSchema(): array
    {
        return [
            'controlType' => $this->cfg('controlType', Type::Text->value),
            'label'       => $this->cfg('label', ''),
            'description' => $this->cfg('description', ''),
            'placeholder' => $this->cfg('placeholder', ''),
            'default'     => $this->cfg('default', null),
            'required'    => $this->cfg('required', false),
            'readonly'    => $this->cfg('readonly', false),
            'disabled'    => $this->cfg('disabled', false),
            'validation'  => $this->cfg('validation', null),
            'options'     => $this->cfg('options', null),
            'min'         => $this->cfg('min', null),
            'max'         => $this->cfg('max', null),
            'step'        => $this->cfg('step', null),
            'rows'        => $this->cfg('rows', null),
            'multiple'    => $this->cfg('multiple', false),
            'mimeTypes'   => $this->cfg('mime_types', null),
            'postType'    => $this->cfg('post_type', null),
        ];
    }
}
