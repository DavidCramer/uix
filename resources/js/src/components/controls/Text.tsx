import { TextControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

export function Text( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <TextControl
                id={ schema.id }
                value={ value ?? ( schema.default as string ) ?? '' }
                onChange={ onChange }
                placeholder={ schema.placeholder }
                readOnly={ schema.readonly }
                disabled={ schema.disabled }
                type="text"
                // Strip out label/help — ControlWrapper handles those.
                label=""
                hideLabelFromVision
            />
        </ControlWrapper>
    );
}
