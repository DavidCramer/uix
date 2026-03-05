import type { ControlSchema } from '../../types';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

/** Hidden control — renders nothing visible, but participates in data flow. */
export function Hidden( { schema, value, onChange }: Props ) {
    return (
        <input
            type="hidden"
            id={ schema.id }
            value={ String( value ?? schema.default ?? '' ) }
            onChange={ ( e ) => onChange( e.target.value ) }
        />
    );
}
