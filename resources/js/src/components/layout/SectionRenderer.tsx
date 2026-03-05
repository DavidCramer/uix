import type { ControlSchema, RepeatSchema, SectionSchema } from '../../types';
import { ControlRenderer } from './ControlRenderer';
import { RepeatRenderer } from './RepeatRenderer';

interface Props {
    schema: SectionSchema;
    nodeId: string;
}

export function SectionRenderer( { schema, nodeId }: Props ) {
    return (
        <div className="uix-section" id={ schema.id }>
            { schema.title && (
                <h3 className="uix-section__title">{ schema.title }</h3>
            ) }
            { schema.description && (
                <p className="uix-section__description">{ schema.description }</p>
            ) }
            <div className="uix-section__fields">
                { schema.children.map( ( child ) => {
                    if ( child.type === 'repeat' ) {
                        return (
                            <RepeatRenderer
                                key={ child.id }
                                schema={ child as RepeatSchema }
                                nodeId={ nodeId }
                            />
                        );
                    }
                    return (
                        <ControlRenderer
                            key={ child.id }
                            schema={ child as ControlSchema }
                            nodeId={ nodeId }
                        />
                    );
                } ) }
            </div>
        </div>
    );
}
