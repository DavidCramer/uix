import { PanelBody } from '@wordpress/components';
import type { PanelSchema } from '../../types';
import { SectionRenderer } from './SectionRenderer';

interface Props {
    schema: PanelSchema;
    nodeId: string;
}

export function PanelRenderer( { schema, nodeId }: Props ) {
    return (
        <PanelBody
            title={ schema.title }
            initialOpen={ schema.defaultOpen !== false }
            className={ `uix-panel uix-panel--${ schema.slug }` }
        >
            { schema.children.map( ( section ) => (
                <SectionRenderer key={ section.id } schema={ section } nodeId={ nodeId } />
            ) ) }
        </PanelBody>
    );
}
