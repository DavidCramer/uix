import { Button as WPButton } from '@wordpress/components';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    // Button controls don't store a value but may receive an onClick callback
    // via config.onClick (a registered JS callback name).
    onChange?: ( value: unknown ) => void;
}

export function Button( { schema }: Props ) {
    const handleClick = () => {
        const callbackName = schema.config?.onClick as string | undefined;
        if ( callbackName && typeof ( window as Record<string, unknown> )[ callbackName ] === 'function' ) {
            ( ( window as Record<string, unknown> )[ callbackName ] as () => void )();
        }
    };

    const variant = ( schema.config?.variant as 'primary' | 'secondary' | 'tertiary' ) ?? 'secondary';
    const isDestructive = ( schema.config?.destructive as boolean ) ?? false;

    return (
        <ControlWrapper schema={ schema }>
            <WPButton
                variant={ variant }
                isDestructive={ isDestructive }
                disabled={ schema.disabled }
                onClick={ handleClick }
            >
                { schema.label }
            </WPButton>
        </ControlWrapper>
    );
}
