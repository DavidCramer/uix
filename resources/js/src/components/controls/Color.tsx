import { ColorPicker } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

export function Color( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <ColorPicker
                color={ value ?? ( schema.default as string ) ?? '#000000' }
                onChange={ onChange }
                enableAlpha
                defaultValue={ ( schema.default as string ) ?? '#000000' }
            />
        </ControlWrapper>
    );
}
