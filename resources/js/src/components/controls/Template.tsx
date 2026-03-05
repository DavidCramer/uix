/**
 * Template control — renders arbitrary HTML/React markup from a registered
 * template function. The function must be registered on window before mount.
 *
 * PHP usage:
 *   ->template('my_template', [
 *       'label' => 'Custom HTML',
 *       'config' => ['templateFn' => 'myPluginTemplate'],
 *   ])
 *
 * JS usage (in plugin's own script):
 *   window.myPluginTemplate = ({ value, onChange, schema }) => <div>...</div>;
 */
import type { ControlSchema, FieldValues } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface TemplateFnProps {
    schema: ControlSchema;
    value: unknown;
    onChange: ( value: unknown ) => void;
    allValues: FieldValues;
}

interface Props {
    schema: ControlSchema;
    value: unknown;
    onChange: ( value: unknown ) => void;
    allValues?: FieldValues;
}

export function Template( { schema, value, onChange, allValues = {} }: Props ) {
    const fnName = schema.config?.templateFn as string | undefined;
    const fn = fnName ? ( window as Record<string, unknown> )[ fnName ] : undefined;

    if ( typeof fn !== 'function' ) {
        return (
            <ControlWrapper schema={ schema }>
                <p style={ { color: 'red', fontSize: 12 } }>
                    UIX Template: function <code>{ fnName ?? '(none)' }</code> not found.
                </p>
            </ControlWrapper>
        );
    }

    return (
        <ControlWrapper schema={ schema }>
            { ( fn as ( props: TemplateFnProps ) => React.ReactNode )( {
                schema,
                value,
                onChange,
                allValues,
            } ) }
        </ControlWrapper>
    );
}
