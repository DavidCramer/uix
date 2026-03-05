import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: number | string;
    onChange: ( value: string | undefined ) => void;
}

export function Number( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <NumberControl
                id={ schema.id }
                value={ String( value ?? schema.default ?? '' ) }
                onChange={ onChange }
                placeholder={ schema.placeholder }
                min={ schema.min ?? undefined }
                max={ schema.max ?? undefined }
                step={ schema.step ?? 1 }
                disabled={ schema.disabled }
                isShiftStepEnabled
                label=""
                hideLabelFromVision
            />
        </ControlWrapper>
    );
}
