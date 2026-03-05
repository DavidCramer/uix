import type { ControlSchema } from '../../types';

interface Props {
    schema: ControlSchema;
}

export function Separator( { schema }: Props ) {
    return (
        <div className="uix-control uix-control--separator">
            { schema.label && (
                <h3 className="uix-separator__label">{ schema.label }</h3>
            ) }
            <hr className="uix-separator__line" />
            { schema.description && (
                <p className="uix-separator__description">{ schema.description }</p>
            ) }
        </div>
    );
}
