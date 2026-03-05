// ---------------------------------------------------------------------------
// Control types — keep in sync with src/UI/Control/Type.php
// ---------------------------------------------------------------------------
export type ControlType =
    | 'text'
    | 'textarea'
    | 'email'
    | 'number'
    | 'hidden'
    | 'checkbox'
    | 'radio'
    | 'select'
    | 'color'
    | 'date'
    | 'image'
    | 'file'
    | 'editor'
    | 'toggle'
    | 'slider'
    | 'autocomplete'
    | 'button'
    | 'separator'
    | 'template'
    | 'post_relation';

// ---------------------------------------------------------------------------
// Option shape used by select / radio
// ---------------------------------------------------------------------------
export interface SelectOption {
    value: string | number;
    label: string;
    disabled?: boolean;
}

// ---------------------------------------------------------------------------
// Control schema — leaf node
// ---------------------------------------------------------------------------
export interface ControlSchema {
    id: string;
    slug: string;
    type: 'control';
    controlType: ControlType;
    label: string;
    description: string;
    placeholder: string;
    default: unknown;
    required: boolean;
    readonly: boolean;
    disabled: boolean;
    validation: ValidationRules | null;
    options: SelectOption[] | null;
    min: number | null;
    max: number | null;
    step: number | null;
    rows: number | null;
    multiple: boolean;
    mimeTypes: string[] | null;
    postType: string | null;
    config: Record<string, unknown>;
    children: [];
}

export interface ValidationRules {
    min?: number;
    max?: number;
    minLength?: number;
    maxLength?: number;
    pattern?: string;
    message?: string;
}

// ---------------------------------------------------------------------------
// Repeat schema
// ---------------------------------------------------------------------------
export interface RepeatSchema {
    id: string;
    slug: string;
    type: 'repeat';
    label: string;
    min: number;
    max: number | null;
    addLabel: string;
    removeLabel: string;
    config: Record<string, unknown>;
    children: ControlSchema[];
}

// ---------------------------------------------------------------------------
// Section schema
// ---------------------------------------------------------------------------
export interface SectionSchema {
    id: string;
    slug: string;
    type: 'section';
    title: string;
    description: string;
    config: Record<string, unknown>;
    children: Array<ControlSchema | RepeatSchema>;
}

// ---------------------------------------------------------------------------
// Panel schema
// ---------------------------------------------------------------------------
export interface PanelSchema {
    id: string;
    slug: string;
    type: 'panel';
    title: string;
    icon: string;
    collapsible: boolean;
    defaultOpen: boolean;
    config: Record<string, unknown>;
    children: SectionSchema[];
}

// ---------------------------------------------------------------------------
// Data config embedded in Page / Metabox schemas
// ---------------------------------------------------------------------------
export interface DataConfig {
    source: 'options' | 'post_meta';
    storeKey: string;
    nonce: string;
    restBase: string;
}

// ---------------------------------------------------------------------------
// Page schema (root for classic admin pages)
// ---------------------------------------------------------------------------
export interface PageSchema {
    id: string;
    slug: string;
    type: 'page';
    title: string;
    capability: string;
    color: string;
    data: DataConfig;
    config: Record<string, unknown>;
    children: Array<PanelSchema | SectionSchema>;
}

// ---------------------------------------------------------------------------
// Metabox schema (root for post editor metaboxes)
// ---------------------------------------------------------------------------
export interface MetaboxSchema {
    id: string;
    slug: string;
    type: 'metabox';
    name: string;
    context: string;
    priority: string;
    color: string;
    data: DataConfig;
    config: Record<string, unknown>;
    children: Array<PanelSchema | SectionSchema>;
}

// ---------------------------------------------------------------------------
// Any node (union for the recursive renderer)
// ---------------------------------------------------------------------------
export type AnyNode =
    | PageSchema
    | MetaboxSchema
    | PanelSchema
    | SectionSchema
    | RepeatSchema
    | ControlSchema;

// ---------------------------------------------------------------------------
// Field values — flat map of fieldId → value
// ---------------------------------------------------------------------------
export type FieldValues = Record<string, unknown>;

// ---------------------------------------------------------------------------
// Save state
// ---------------------------------------------------------------------------
export type SaveStatus = 'idle' | 'saving' | 'saved' | 'error';

// ---------------------------------------------------------------------------
// Global window augmentation — set by wp_localize_script
// ---------------------------------------------------------------------------
declare global {
    interface Window {
        uixPageConfigs?: Record<string, PageSchema>;
        uixMetaboxConfigs?: Record<string, MetaboxSchema>;
    }
}
