import { ComboboxControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string | null ) => void;
}

export function Autocomplete( { schema, value, onChange }: Props ) {
    const allOptions = ( schema.options ?? [] ).map( ( o ) => ( {
        value: String( o.value ),
        label: o.label,
    } ) );

    const [ filtered, setFiltered ] = useState( allOptions );

    const handleFilter = ( input: string ) => {
        const lower = input.toLowerCase();
        setFiltered( allOptions.filter( ( o ) => o.label.toLowerCase().includes( lower ) ) );
    };

    return (
        <ControlWrapper schema={ schema }>
            <ComboboxControl
                value={ value ?? String( schema.default ?? '' ) }
                options={ filtered }
                onChange={ onChange }
                onFilterValueChange={ handleFilter }
                label=""
                hideLabelFromVision
                disabled={ schema.disabled }
            />
        </ControlWrapper>
    );
}
