import { RangeControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: number;
    onChange: ( value: number | undefined ) => void;
}

export function Slider( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <RangeControl
                value={ value ?? ( schema.default as number ) ?? 0 }
                onChange={ onChange }
                min={ schema.min ?? 0 }
                max={ schema.max ?? 100 }
                step={ schema.step ?? 1 }
                disabled={ schema.disabled }
                label=""
                hideLabelFromVision
            />
        </ControlWrapper>
    );
}
