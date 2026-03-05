<?php

declare(strict_types=1);

namespace UIX\UI\Control;

/**
 * All supported control types.
 * The string value matches the React component name used in the frontend.
 */
enum Type: string
{
    case Text         = 'text';
    case Textarea     = 'textarea';
    case Email        = 'email';
    case Number       = 'number';
    case Hidden       = 'hidden';
    case Checkbox     = 'checkbox';
    case Radio        = 'radio';
    case Select       = 'select';
    case Color        = 'color';
    case Date         = 'date';
    case Image        = 'image';
    case File         = 'file';
    case Editor       = 'editor';
    case Toggle       = 'toggle';
    case Slider       = 'slider';
    case Autocomplete = 'autocomplete';
    case Button       = 'button';
    case Separator    = 'separator';
    case Template     = 'template';
    case PostRelation = 'post_relation';
}
