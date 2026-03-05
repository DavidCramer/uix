/**
 * UIX REST API client.
 *
 * Wraps @wordpress/api-fetch to hit the /uix/v1/data/ endpoints.
 */
import apiFetch from '@wordpress/api-fetch';
import type { DataConfig, FieldValues } from '../types';

/**
 * Load field values for a UIX node.
 */
export async function loadData(
    config: DataConfig,
    postId?: number,
): Promise<FieldValues> {
    const path = postId
        ? `/uix/v1/data/${ config.storeKey }/${ postId }`
        : `/uix/v1/data/${ config.storeKey }`;

    const response = await apiFetch<{ data: FieldValues }>( {
        path,
        method: 'GET',
    } );

    return response.data ?? {};
}

/**
 * Save field values for a UIX node.
 */
export async function saveData(
    config: DataConfig,
    values: FieldValues,
    postId?: number,
): Promise<FieldValues> {
    const path = postId
        ? `/uix/v1/data/${ config.storeKey }/${ postId }`
        : `/uix/v1/data/${ config.storeKey }`;

    const response = await apiFetch<{ success: boolean; data: FieldValues }>( {
        path,
        method: 'POST',
        data: values,
    } );

    return response.data ?? values;
}
