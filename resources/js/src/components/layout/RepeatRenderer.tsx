/**
 * RepeatRenderer — renders a repeatable group of controls.
 *
 * Each row is a copy of the schema's children; rows are stored as an array
 * of FieldValues objects in the parent store field.
 */
import { Button } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import type { ControlSchema, RepeatSchema } from '../../types';
import { STORE_NAME } from '../../store';
import {
    Autocomplete, Checkbox, Color, Date, Email, File,
    Hidden, Image, Number, PostRelation, Radio, Select,
    Separator, Slider, Template, Text, Textarea, Toggle,
} from '../controls';

interface Props {
    schema: RepeatSchema;
    nodeId: string;
}

type RowData = Record<string, unknown>;

export function RepeatRenderer( { schema, nodeId }: Props ) {
    const { setField } = useDispatch( STORE_NAME );
    const rows = useSelect<RowData[]>(
        ( select ) => {
            const v = select( STORE_NAME ).getFieldValue( nodeId, schema.slug );
            return Array.isArray( v ) ? ( v as RowData[] ) : [];
        },
        [ nodeId, schema.slug ],
    );

    const setRows = ( next: RowData[] ) => setField( nodeId, schema.slug, next );

    const addRow = () => {
        if ( schema.max !== null && rows.length >= schema.max ) return;
        const emptyRow: RowData = {};
        schema.children.forEach( ( c ) => { emptyRow[ c.slug ] = c.default ?? null; } );
        setRows( [ ...rows, emptyRow ] );
    };

    const removeRow = ( index: number ) => {
        setRows( rows.filter( ( _, i ) => i !== index ) );
    };

    const updateRowField = ( rowIndex: number, fieldSlug: string, value: unknown ) => {
        const next = rows.map( ( row, i ) =>
            i === rowIndex ? { ...row, [ fieldSlug ]: value } : row,
        );
        setRows( next );
    };

    const renderControl = ( control: ControlSchema, rowIndex: number ) => {
        const value = rows[ rowIndex ]?.[ control.slug ];
        const onChange = ( v: unknown ) => updateRowField( rowIndex, control.slug, v );
        const p = { schema: control, value, onChange };

        switch ( control.controlType ) {
            case 'text':         return <Text { ...p as Parameters<typeof Text>[0] } />;
            case 'textarea':     return <Textarea { ...p as Parameters<typeof Textarea>[0] } />;
            case 'email':        return <Email { ...p as Parameters<typeof Email>[0] } />;
            case 'number':       return <Number { ...p as Parameters<typeof Number>[0] } />;
            case 'hidden':       return <Hidden { ...p as Parameters<typeof Hidden>[0] } />;
            case 'checkbox':     return <Checkbox { ...p as Parameters<typeof Checkbox>[0] } />;
            case 'radio':        return <Radio { ...p as Parameters<typeof Radio>[0] } />;
            case 'select':       return <Select { ...p as Parameters<typeof Select>[0] } />;
            case 'color':        return <Color { ...p as Parameters<typeof Color>[0] } />;
            case 'date':         return <Date { ...p as Parameters<typeof Date>[0] } />;
            case 'image':        return <Image { ...p as Parameters<typeof Image>[0] } />;
            case 'file':         return <File { ...p as Parameters<typeof File>[0] } />;
            case 'toggle':       return <Toggle { ...p as Parameters<typeof Toggle>[0] } />;
            case 'slider':       return <Slider { ...p as Parameters<typeof Slider>[0] } />;
            case 'autocomplete': return <Autocomplete { ...p as Parameters<typeof Autocomplete>[0] } />;
            case 'post_relation': return <PostRelation { ...p as Parameters<typeof PostRelation>[0] } />;
            case 'separator':    return <Separator schema={ control } />;
            case 'template':     return <Template schema={ control } value={ value } onChange={ onChange } />;
            default:             return null;
        }
    };

    return (
        <div className="uix-repeat">
            { schema.label && <h4 className="uix-repeat__label">{ schema.label }</h4> }

            { rows.map( ( _row, rowIndex ) => (
                <div key={ rowIndex } className="uix-repeat__row">
                    <div className="uix-repeat__row-fields">
                        { schema.children.map( ( control ) => (
                            <div key={ control.slug }>
                                { renderControl( control, rowIndex ) }
                            </div>
                        ) ) }
                    </div>
                    <Button
                        variant="tertiary"
                        isDestructive
                        className="uix-repeat__remove"
                        onClick={ () => removeRow( rowIndex ) }
                        disabled={ rows.length <= ( schema.min ?? 0 ) }
                    >
                        { schema.removeLabel || __( 'Remove', 'uix' ) }
                    </Button>
                </div>
            ) ) }

            <Button
                variant="secondary"
                className="uix-repeat__add"
                onClick={ addRow }
                disabled={ schema.max !== null && rows.length >= schema.max }
            >
                { schema.addLabel || __( 'Add Item', 'uix' ) }
            </Button>
        </div>
    );
}
