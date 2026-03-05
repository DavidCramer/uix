import { SelectControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string | string[];
    onChange: ( value: string ) => void;
}

export function Select( { schema, value, onChange }: Props ) {
    const opts = ( schema.options ?? [] ).map( ( o ) => ( {
        value: String( o.value ),
        label: o.label,
        disabled: o.disabled,
    } ) );

    return (
        <ControlWrapper schema={ schema }>
            <SelectControl
                id={ schema.id }
                value={ value as string ?? String( schema.default ?? '' ) }
                options={ opts }
                onChange={ onChange }
                multiple={ schema.multiple }
                disabled={ schema.disabled }
                label=""
                hideLabelFromVision
            />
        </ControlWrapper>
    );
}
