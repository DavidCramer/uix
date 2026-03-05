/**
 * ClassicMetabox — mounts a UIX metabox in the classic post editor.
 *
 * PHP renders:
 *   <div id="uix-metabox-{slug}" class="uix-metabox-root" data-post-id="{id}"></div>
 *
 * On mount we read the post ID from the DOM element's data attribute, register
 * the node in the store, then load and render saved post meta.
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import type { MetaboxSchema } from '../../types';
import { STORE_NAME } from '../../store';
import { NodeRenderer } from '../../components/layout/NodeRenderer';
import { SaveBar } from '../../components/ui/SaveBar';

interface Props {
    schema: MetaboxSchema;
    postId: number;
}

export function ClassicMetabox( { schema, postId }: Props ) {
    const { registerNode, loadValues } = useDispatch( STORE_NAME );

    const isRegistered = useSelect<boolean>(
        ( select ) => !! select( STORE_NAME ).getSchema( schema.id ),
        [ schema.id ],
    );

    useEffect( () => {
        registerNode( schema.id, schema, postId );
        loadValues( schema.id );
    }, [ schema.id, postId ] );

    if ( ! isRegistered ) {
        return <div className="uix-loading" aria-busy="true" />;
    }

    return (
        <div
            className="uix-metabox"
            id={ `uix-metabox-inner-${ schema.slug }` }
            style={ { '--uix-color': schema.color } as React.CSSProperties }
        >
            <div className="uix-metabox__body">
                <NodeRenderer schema={ schema } />
            </div>
            <div className="uix-metabox__footer">
                <SaveBar nodeId={ schema.id } />
            </div>
        </div>
    );
}
