/**
 * NodeRenderer — the recursive entry point.
 *
 * Accepts a Page or Metabox schema and renders its children tree.
 * Children can be Panels or Sections (both of which contain controls/repeats).
 */
import type { MetaboxSchema, PageSchema, PanelSchema, SectionSchema } from '../../types';
import { PanelRenderer } from './PanelRenderer';
import { SectionRenderer } from './SectionRenderer';

interface Props {
    schema: PageSchema | MetaboxSchema;
}

export function NodeRenderer( { schema }: Props ) {
    return (
        <div className={ `uix-node uix-node--${ schema.type }` }>
            { schema.children.map( ( child ) => {
                if ( child.type === 'panel' ) {
                    return (
                        <PanelRenderer
                            key={ child.id }
                            schema={ child as PanelSchema }
                            nodeId={ schema.id }
                        />
                    );
                }
                if ( child.type === 'section' ) {
                    return (
                        <SectionRenderer
                            key={ child.id }
                            schema={ child as SectionSchema }
                            nodeId={ schema.id }
                        />
                    );
                }
                return null;
            } ) }
        </div>
    );
}
