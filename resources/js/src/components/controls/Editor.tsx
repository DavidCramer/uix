/**
 * Rich-text editor control using the WordPress TinyMCE editor.
 * We use a simple textarea here as a progressively-enhanced fallback —
 * in practice, wpEditor.initialize() is called after mount to activate TinyMCE.
 */
import { useEffect, useRef } from '@wordpress/element';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface Props {
    schema: ControlSchema;
    value: string;
    onChange: ( value: string ) => void;
}

declare const wp: {
    editor: {
        initialize: ( id: string, settings: Record<string, unknown> ) => void;
        remove: ( id: string ) => void;
    };
};

export function Editor( { schema, value, onChange }: Props ) {
    const editorId = `uix-editor-${ schema.id }`;
    const initialized = useRef( false );

    useEffect( () => {
        if ( initialized.current || typeof wp === 'undefined' || ! wp.editor ) return;
        initialized.current = true;

        wp.editor.initialize( editorId, {
            tinymce: {
                wpautop: true,
                setup( editor: { on: ( e: string, cb: ( evt: { target: { getContent: () => string } } ) => void ) => void } ) {
                    editor.on( 'change keyup', ( evt ) => {
                        onChange( evt.target.getContent() );
                    } );
                },
            },
            quicktags: true,
            mediaButtons: true,
        } );

        return () => {
            if ( typeof wp !== 'undefined' && wp.editor ) {
                wp.editor.remove( editorId );
                initialized.current = false;
            }
        };
    }, [] );

    return (
        <ControlWrapper schema={ schema }>
            <div className="wp-editor-wrap">
                <textarea
                    id={ editorId }
                    name={ editorId }
                    defaultValue={ value ?? ( schema.default as string ) ?? '' }
                    rows={ schema.rows ?? 10 }
                    className="wp-editor-area"
                    style={ { width: '100%' } }
                    readOnly={ schema.readonly }
                    disabled={ schema.disabled }
                />
            </div>
        </ControlWrapper>
    );
}
