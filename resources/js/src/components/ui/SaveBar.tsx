/**
 * Sticky save bar shown at the bottom of admin pages.
 * Displays save status and a Save button.
 */
import { Button, Spinner } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import type { SaveStatus } from '../../types';
import { STORE_NAME } from '../../store';

interface Props {
    nodeId: string;
}

const statusLabel: Record<SaveStatus, string> = {
    idle:   '',
    saving: __( 'Saving…', 'uix' ),
    saved:  __( 'Saved', 'uix' ),
    error:  __( 'Error — could not save', 'uix' ),
};

export function SaveBar( { nodeId }: Props ) {
    const { saveValues } = useDispatch( STORE_NAME );
    const saveStatus = useSelect<SaveStatus>(
        ( select ) => select( STORE_NAME ).getSaveStatus( nodeId ),
        [ nodeId ],
    );
    const isDirty = useSelect<boolean>(
        ( select ) => select( STORE_NAME ).isDirty( nodeId ),
        [ nodeId ],
    );
    const error = useSelect<string | null>(
        ( select ) => select( STORE_NAME ).getSaveError( nodeId ),
        [ nodeId ],
    );

    const isSaving = saveStatus === 'saving';

    return (
        <div
            className={ `uix-save-bar uix-save-bar--${ saveStatus }` }
            style={ { display: 'flex', alignItems: 'center', gap: 12, padding: '12px 0' } }
        >
            <Button
                variant="primary"
                onClick={ () => saveValues( nodeId ) }
                isBusy={ isSaving }
                disabled={ isSaving || ( ! isDirty && saveStatus !== 'error' ) }
            >
                { isSaving ? __( 'Saving…', 'uix' ) : __( 'Save Changes', 'uix' ) }
            </Button>

            { isSaving && <Spinner /> }

            { saveStatus !== 'idle' && (
                <span
                    className={ `uix-save-bar__status uix-save-bar__status--${ saveStatus }` }
                    style={ { color: saveStatus === 'error' ? '#cc1818' : '#2a8a2a', fontSize: 13 } }
                >
                    { saveStatus === 'error' ? ( error ?? statusLabel.error ) : statusLabel[ saveStatus ] }
                </span>
            ) }
        </div>
    );
}
