import { ToggleControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: boolean;
    onChange: ( value: boolean ) => void;
}

export function Toggle( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <ToggleControl
                checked={ value ?? ( schema.default as boolean ) ?? false }
                onChange={ onChange }
                disabled={ schema.disabled }
                label={ schema.label }
            />
        </ControlWrapper>
    );
}
