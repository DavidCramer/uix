import { TextControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

export function Email( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <TextControl
                id={ schema.id }
                type="email"
                value={ value ?? ( schema.default as string ) ?? '' }
                onChange={ onChange }
                placeholder={ schema.placeholder }
                readOnly={ schema.readonly }
                disabled={ schema.disabled }
                label=""
                hideLabelFromVision
            />
        </ControlWrapper>
    );
}
