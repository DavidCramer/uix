/**
 * Gutenberg adapter bootstrapper.
 *
 * Registers each UIX metabox as a block editor plugin (PluginDocumentSettingPanel).
 * Called from gutenberg.tsx once WP is ready.
 */
import { registerPlugin } from '@wordpress/plugins';
import { GutenbergPanel } from './GutenbergPanel';
import '../../store'; // ensure store is registered

export function registerGutenbergPanels(): void {
    const configs = window.uixMetaboxConfigs ?? {};

    Object.entries( configs ).forEach( ( [ slug, schema ] ) => {
        registerPlugin( `uix-metabox-${ slug }`, {
            render: () => <GutenbergPanel schema={ schema } />,
        } );
    } );
}
