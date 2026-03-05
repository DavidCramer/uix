/**
 * UIX Classic Admin entry point.
 *
 * Mounts React components for UIX pages and metaboxes in the
 * classic WordPress admin (non-block-editor screens).
 */
import { mountClassicMetaboxes, mountClassicPages } from './src/adapters/classic';

function boot() {
    mountClassicPages();
    mountClassicMetaboxes();
}

if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', boot );
} else {
    boot();
}
