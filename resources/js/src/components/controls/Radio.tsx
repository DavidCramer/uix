import { RadioControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

export function Radio( { schema, value, onChange }: Props ) {
    const opts = ( schema.options ?? [] ).map( ( o ) => ( {
        value: String( o.value ),
        label: o.label,
    } ) );

    return (
        <ControlWrapper schema={ schema }>
            <RadioControl
                selected={ value ?? String( schema.default ?? '' ) }
                options={ opts }
                onChange={ onChange }
                disabled={ schema.disabled }
                label=""
            />
        </ControlWrapper>
    );
}
