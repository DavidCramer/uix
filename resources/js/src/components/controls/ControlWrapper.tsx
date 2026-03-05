/**
 * Shared wrapper rendered around every control.
 * Provides label, description, and consistent layout.
 */
import { BaseControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';

interface Props {
    schema: ControlSchema;
    children: React.ReactNode;
}

export function ControlWrapper( { schema, children }: Props ) {
    return (
        <BaseControl
            id={ schema.id }
            label={ schema.label || undefined }
            help={ schema.description || undefined }
            className={ `uix-control uix-control--${ schema.controlType }` }
        >
            { children }
        </BaseControl>
    );
}
