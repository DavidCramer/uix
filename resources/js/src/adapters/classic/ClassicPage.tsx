/**
 * ClassicPage — mounts a UIX settings page in the classic WP admin.
 *
 * PHP renders:
 *   <div id="uix-page-{slug}" class="uix-page-root"></div>
 *
 * This component finds that element, reads the schema from window.uixPageConfigs,
 * initialises the store, loads saved data, and renders the form + save bar.
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import type { PageSchema, SaveStatus } from '../../types';
import { STORE_NAME } from '../../store';
import { NodeRenderer } from '../../components/layout/NodeRenderer';
import { SaveBar } from '../../components/ui/SaveBar';

interface Props {
    schema: PageSchema;
}

export function ClassicPage( { schema }: Props ) {
    const { registerNode, loadValues } = useDispatch( STORE_NAME );

    const isRegistered = useSelect<boolean>(
        ( select ) => !! select( STORE_NAME ).getSchema( schema.id ),
        [ schema.id ],
    );
    const saveStatus = useSelect<SaveStatus>(
        ( select ) => select( STORE_NAME ).getSaveStatus( schema.id ),
        [ schema.id ],
    );

    useEffect( () => {
        registerNode( schema.id, schema );
        loadValues( schema.id );
    }, [ schema.id ] );

    if ( ! isRegistered ) {
        return <div className="uix-loading" aria-busy="true" />;
    }

    return (
        <div
            className="uix-page"
            id={ `uix-page-${ schema.slug }` }
            style={ { '--uix-color': schema.color } as React.CSSProperties }
        >
            <div className="uix-page__header">
                <h1 className="uix-page__title">{ schema.title }</h1>
            </div>

            <div className="uix-page__body">
                <NodeRenderer schema={ schema } />
            </div>

            <div className="uix-page__footer">
                <SaveBar nodeId={ schema.id } />
            </div>
        </div>
    );
}
