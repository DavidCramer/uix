import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface FileValue {
    id: number;
    url: string;
    filename?: string;
}

interface Props {
    schema: ControlSchema;
    value: FileValue | null;
    onChange: ( value: FileValue | null ) => void;
}

declare const wp: {
    media: ( options: Record<string, unknown> ) => {
        open: () => void;
        on: ( event: string, cb: () => void ) => void;
        state: () => { get: ( key: string ) => { first: () => { toJSON: () => { id: number; url: string; filename: string } } } };
    };
};

export function File( { schema, value, onChange }: Props ) {
    const openMediaFrame = () => {
        if ( typeof wp === 'undefined' || ! wp.media ) return;

        const frame = wp.media( {
            title: schema.label || __( 'Select File', 'uix' ),
            button: { text: __( 'Use File', 'uix' ) },
            multiple: false,
            library: { type: schema.mimeTypes ?? undefined },
        } );

        frame.on( 'select', () => {
            const attachment = frame.state().get( 'selection' ).first().toJSON();
            onChange( { id: attachment.id, url: attachment.url, filename: attachment.filename } );
        } );

        frame.open();
    };

    return (
        <ControlWrapper schema={ schema }>
            { value?.filename && (
                <p className="uix-control--file__name" style={ { marginBottom: 8 } }>
                    📎 { value.filename }
                </p>
            ) }
            <div style={ { display: 'flex', gap: 8 } }>
                <Button variant="secondary" onClick={ openMediaFrame } disabled={ schema.disabled }>
                    { value ? __( 'Change File', 'uix' ) : __( 'Select File', 'uix' ) }
                </Button>
                { value && (
                    <Button variant="tertiary" isDestructive onClick={ () => onChange( null ) } disabled={ schema.disabled }>
                        { __( 'Remove', 'uix' ) }
                    </Button>
                ) }
            </div>
        </ControlWrapper>
    );
}
