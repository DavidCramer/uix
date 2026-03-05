/**
 * UIX Gutenberg entry point.
 *
 * Registers UIX metaboxes as block editor sidebar panels using the
 * WordPress Plugins API.
 */
import { registerGutenbergPanels } from './src/adapters/gutenberg';

registerGutenbergPanels();
