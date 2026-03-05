/**
 * Post relation control — search and select one or more posts of a given type.
 * Uses the WordPress REST API to search posts.
 */
import apiFetch from '@wordpress/api-fetch';
import { ComboboxControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import type { ControlSchema } from '../../types';
import { ControlWrapper } from './ControlWrapper';

interface WPPost {
    id: number;
    title: { rendered: string };
}

interface PostOption {
    value: string;
    label: string;
}

interface Props {
    schema: ControlSchema;
    value: string | number | null;
    onChange: ( value: string | null ) => void;
}

export function PostRelation( { schema, value, onChange }: Props ) {
    const [ options, setOptions ] = useState<PostOption[]>( [] );
    const [ isLoading, setIsLoading ] = useState( false );
    const postType = schema.postType ?? 'post';

    const search = async ( input: string ) => {
        setIsLoading( true );
        try {
            const results = await apiFetch<WPPost[]>( {
                path: `/wp/v2/${ postType }?search=${ encodeURIComponent( input ) }&per_page=20&_fields=id,title`,
            } );
            setOptions( results.map( ( p ) => ( {
                value: String( p.id ),
                label: p.title.rendered,
            } ) ) );
        } catch {
            // swallow fetch errors; search field just shows no results.
        } finally {
            setIsLoading( false );
        }
    };

    useEffect( () => { search( '' ); }, [] );

    return (
        <ControlWrapper schema={ schema }>
            <ComboboxControl
                value={ value !== null ? String( value ) : '' }
                options={ options }
                onChange={ onChange }
                onFilterValueChange={ search }
                label=""
                hideLabelFromVision
                disabled={ schema.disabled || isLoading }
                messages={ { selected: __( 'Selected', 'uix' ) } }
            />
        </ControlWrapper>
    );
}
