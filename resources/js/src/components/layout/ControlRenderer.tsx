/**
 * ControlRenderer — maps a ControlSchema to the right React component
 * and wires it to the UIX store.
 */
import { useDispatch, useSelect } from '@wordpress/data';
import type { ControlSchema } from '../../types';
import {
    Autocomplete,
    Button,
    Checkbox,
    Color,
    Date,
    Editor,
    Email,
    File,
    Hidden,
    Image,
    Number,
    PostRelation,
    Radio,
    Select,
    Separator,
    Slider,
    Template,
    Text,
    Textarea,
    Toggle,
} from '../controls';
import { STORE_NAME } from '../../store';

interface Props {
    schema: ControlSchema;
    nodeId: string; // the Page or Metabox id this control belongs to
}

export function ControlRenderer( { schema, nodeId }: Props ) {
    const { setField } = useDispatch( STORE_NAME );
    const value = useSelect( ( select ) => select( STORE_NAME ).getFieldValue( nodeId, schema.slug ), [ nodeId, schema.slug ] );
    const allValues = useSelect( ( select ) => select( STORE_NAME ).getValues( nodeId ), [ nodeId ] );

    const onChange = ( newValue: unknown ) => setField( nodeId, schema.slug, newValue );

    const commonProps = { schema, value, onChange } as Parameters<typeof Text>[0];

    switch ( schema.controlType ) {
        case 'text':         return <Text { ...commonProps as Parameters<typeof Text>[0] } />;
        case 'textarea':     return <Textarea { ...commonProps as Parameters<typeof Textarea>[0] } />;
        case 'email':        return <Email { ...commonProps as Parameters<typeof Email>[0] } />;
        case 'number':       return <Number { ...commonProps as Parameters<typeof Number>[0] } />;
        case 'hidden':       return <Hidden { ...commonProps as Parameters<typeof Hidden>[0] } />;
        case 'checkbox':     return <Checkbox { ...commonProps as Parameters<typeof Checkbox>[0] } />;
        case 'radio':        return <Radio { ...commonProps as Parameters<typeof Radio>[0] } />;
        case 'select':       return <Select { ...commonProps as Parameters<typeof Select>[0] } />;
        case 'color':        return <Color { ...commonProps as Parameters<typeof Color>[0] } />;
        case 'date':         return <Date { ...commonProps as Parameters<typeof Date>[0] } />;
        case 'image':        return <Image { ...commonProps as Parameters<typeof Image>[0] } />;
        case 'file':         return <File { ...commonProps as Parameters<typeof File>[0] } />;
        case 'editor':       return <Editor { ...commonProps as Parameters<typeof Editor>[0] } />;
        case 'toggle':       return <Toggle { ...commonProps as Parameters<typeof Toggle>[0] } />;
        case 'slider':       return <Slider { ...commonProps as Parameters<typeof Slider>[0] } />;
        case 'autocomplete': return <Autocomplete { ...commonProps as Parameters<typeof Autocomplete>[0] } />;
        case 'button':       return <Button schema={ schema } />;
        case 'separator':    return <Separator schema={ schema } />;
        case 'post_relation': return <PostRelation { ...commonProps as Parameters<typeof PostRelation>[0] } />;
        case 'template':
            return <Template schema={ schema } value={ value } onChange={ onChange } allValues={ allValues } />;

        default:
            return (
                <p style={ { color: 'red', fontSize: 12 } }>
                    UIX: unknown control type <code>{ ( schema as ControlSchema ).controlType }</code>
                </p>
            );
    }
}
