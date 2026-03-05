/**
 * Classic admin adapter bootstrapper.
 *
 * Finds all UIX page/metabox root elements in the DOM and mounts React trees.
 * Called from classic.tsx on DOMContentLoaded.
 */
import { render } from '@wordpress/element';
import { ClassicPage } from './ClassicPage';
import { ClassicMetabox } from './ClassicMetabox';
import '../../store'; // ensure store is registered

export function mountClassicPages(): void {
    const configs = window.uixPageConfigs ?? {};

    Object.entries( configs ).forEach( ( [ slug, schema ] ) => {
        const el = document.getElementById( `uix-page-${ slug }` );
        if ( ! el ) return;
        render( <ClassicPage schema={ schema } />, el );
    } );
}

export function mountClassicMetaboxes(): void {
    const configs = window.uixMetaboxConfigs ?? {};

    Object.entries( configs ).forEach( ( [ slug, schema ] ) => {
        const el = document.getElementById( `uix-metabox-${ slug }` );
        if ( ! el ) return;

        const postId = parseInt( el.dataset.postId ?? '0', 10 );
        render( <ClassicMetabox schema={ schema } postId={ postId } />, el );
    } );
}
