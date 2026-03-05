/**
 * GutenbergPanel — renders a UIX metabox as a Gutenberg block editor sidebar panel.
 *
 * Uses @wordpress/edit-post's PluginSidebar/PluginDocumentSettingPanel to insert
 * the UIX controls into the block editor UI.
 *
 * The current post ID is read from the WP block editor data store.
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEffect } from '@wordpress/element';
import type { MetaboxSchema } from '../../types';
import { STORE_NAME } from '../../store';
import { NodeRenderer } from '../../components/layout/NodeRenderer';
import { SaveBar } from '../../components/ui/SaveBar';

interface Props {
    schema: MetaboxSchema;
}

export function GutenbergPanel( { schema }: Props ) {
    const { registerNode, loadValues } = useDispatch( STORE_NAME );

    // Get the current post ID from the block editor store.
    const postId = useSelect<number>(
        ( select ) => ( select( 'core/editor' ) as { getCurrentPostId: () => number } ).getCurrentPostId(),
        [],
    );

    const isRegistered = useSelect<boolean>(
        ( select ) => !! select( STORE_NAME ).getSchema( schema.id ),
        [ schema.id ],
    );

    useEffect( () => {
        if ( ! postId ) return;
        registerNode( schema.id, schema, postId );
        loadValues( schema.id );
    }, [ schema.id, postId ] );

    if ( ! isRegistered ) {
        return null;
    }

    return (
        <PluginDocumentSettingPanel
            name={ `uix-panel-${ schema.slug }` }
            title={ schema.name }
            className={ `uix-gutenberg-panel uix-gutenberg-panel--${ schema.slug }` }
        >
            <NodeRenderer schema={ schema } />
            <SaveBar nodeId={ schema.id } />
        </PluginDocumentSettingPanel>
    );
}
