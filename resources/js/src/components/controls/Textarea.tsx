import { TextareaControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

export function Textarea( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <TextareaControl
                id={ schema.id }
                value={ value ?? ( schema.default as string ) ?? '' }
                onChange={ onChange }
                placeholder={ schema.placeholder }
                rows={ schema.rows ?? 4 }
                label=""
                hideLabelFromVision
                disabled={ schema.disabled }
                readOnly={ schema.readonly }
            />
        </ControlWrapper>
    );
}
