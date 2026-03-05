import { DatePicker } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string | null ) => void;
}

export function Date( { schema, value, onChange }: Props ) {
    return (
        <ControlWrapper schema={ schema }>
            <DatePicker
                currentDate={ value ?? ( schema.default as string ) ?? undefined }
                onChange={ onChange }
            />
        </ControlWrapper>
    );
}
