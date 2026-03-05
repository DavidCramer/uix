/**
 * Image/media control using the WordPress media frame (wp.media).
 * Falls back gracefully when wp.media is unavailable.
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface MediaValue {
    id: number;
    url: string;
    alt?: string;
    title?: string;
}

interface Props {
    schema: ControlSchema;
    value: MediaValue | null;
    onChange: ( value: MediaValue | null ) => void;
}

declare const wp: {
    media: ( options: Record<string, unknown> ) => {
        open: () => void;
        on: ( event: string, cb: () => void ) => void;
        state: () => { get: ( key: string ) => { first: () => { toJSON: () => { id: number; url: string; alt: string; title: string } } } };
    };
};

export function Image( { schema, value, onChange }: Props ) {
    const openMediaFrame = () => {
        if ( typeof wp === 'undefined' || ! wp.media ) {
            console.warn( 'UIX: wp.media is not available.' );
            return;
        }

        const frame = wp.media( {
            title: schema.label || __( 'Select Image', 'uix' ),
            button: { text: __( 'Use Image', 'uix' ) },
            multiple: false,
            library: { type: schema.mimeTypes ?? [ 'image' ] },
        } );

        frame.on( 'select', () => {
            const attachment = frame.state().get( 'selection' ).first().toJSON();
            onChange( {
                id: attachment.id,
                url: attachment.url,
                alt: attachment.alt,
                title: attachment.title,
            } );
        } );

        frame.open();
    };

    return (
        <ControlWrapper schema={ schema }>
            <div className="uix-control--image__preview">
                { value?.url && (
                    <img
                        src={ value.url }
                        alt={ value.alt ?? '' }
                        style={ { maxWidth: '100%', maxHeight: 200, display: 'block', marginBottom: 8 } }
                    />
                ) }
                <div style={ { display: 'flex', gap: 8 } }>
                    <Button variant="secondary" onClick={ openMediaFrame } disabled={ schema.disabled }>
                        { value ? __( 'Change Image', 'uix' ) : __( 'Select Image', 'uix' ) }
                    </Button>
                    { value && (
                        <Button
                            variant="tertiary"
                            isDestructive
                            onClick={ () => onChange( null ) }
                            disabled={ schema.disabled }
                        >
                            { __( 'Remove', 'uix' ) }
                        </Button>
                    ) }
                </div>
            </div>
        </ControlWrapper>
    );
}
