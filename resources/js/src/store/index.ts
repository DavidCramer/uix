/**
 * UIX @wordpress/data store.
 *
 * Manages:
 *   - schema (from PHP via window.uixPageConfigs / window.uixMetaboxConfigs)
 *   - field values (loaded from / saved to the REST API)
 *   - dirty state (unsaved changes)
 *   - save status per node
 */
import { createReduxStore, register } from '@wordpress/data';
import { loadData, saveData } from '../api';
import type {
    AnyNode,
    DataConfig,
    FieldValues,
    MetaboxSchema,
    PageSchema,
    SaveStatus,
} from '../types';

// ---------------------------------------------------------------------------
// State shape
// ---------------------------------------------------------------------------

interface NodeState {
    schema: PageSchema | MetaboxSchema;
    values: FieldValues;
    savedValues: FieldValues; // last successfully persisted state
    saveStatus: SaveStatus;
    saveError: string | null;
    postId?: number;
}

interface StoreState {
    nodes: Record<string, NodeState>;
}

// ---------------------------------------------------------------------------
// Action types
// ---------------------------------------------------------------------------

type Action =
    | { type: 'REGISTER_NODE'; id: string; schema: PageSchema | MetaboxSchema; postId?: number }
    | { type: 'SET_VALUES'; id: string; values: FieldValues }
    | { type: 'SET_FIELD'; id: string; field: string; value: unknown }
    | { type: 'SAVE_START'; id: string }
    | { type: 'SAVE_SUCCESS'; id: string; values: FieldValues }
    | { type: 'SAVE_ERROR'; id: string; error: string }
    | { type: 'LOAD_SUCCESS'; id: string; values: FieldValues };

// ---------------------------------------------------------------------------
// Reducer
// ---------------------------------------------------------------------------

const DEFAULT_STATE: StoreState = { nodes: {} };

function reducer( state: StoreState = DEFAULT_STATE, action: Action ): StoreState {
    switch ( action.type ) {
        case 'REGISTER_NODE':
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: {
                        schema: action.schema,
                        values: {},
                        savedValues: {},
                        saveStatus: 'idle',
                        saveError: null,
                        postId: action.postId,
                    },
                },
            };

        case 'SET_VALUES': {
            const node = state.nodes[ action.id ];
            if ( ! node ) return state;
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: { ...node, values: action.values },
                },
            };
        }

        case 'SET_FIELD': {
            const node = state.nodes[ action.id ];
            if ( ! node ) return state;
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: {
                        ...node,
                        values: { ...node.values, [ action.field ]: action.value },
                    },
                },
            };
        }

        case 'LOAD_SUCCESS': {
            const node = state.nodes[ action.id ];
            if ( ! node ) return state;
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: {
                        ...node,
                        values: action.values,
                        savedValues: action.values,
                    },
                },
            };
        }

        case 'SAVE_START': {
            const node = state.nodes[ action.id ];
            if ( ! node ) return state;
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: { ...node, saveStatus: 'saving', saveError: null },
                },
            };
        }

        case 'SAVE_SUCCESS': {
            const node = state.nodes[ action.id ];
            if ( ! node ) return state;
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: {
                        ...node,
                        saveStatus: 'saved',
                        savedValues: action.values,
                        saveError: null,
                    },
                },
            };
        }

        case 'SAVE_ERROR': {
            const node = state.nodes[ action.id ];
            if ( ! node ) return state;
            return {
                ...state,
                nodes: {
                    ...state.nodes,
                    [ action.id ]: { ...node, saveStatus: 'error', saveError: action.error },
                },
            };
        }

        default:
            return state;
    }
}

// ---------------------------------------------------------------------------
// Actions (sync)
// ---------------------------------------------------------------------------

const actions = {
    registerNode(
        id: string,
        schema: PageSchema | MetaboxSchema,
        postId?: number,
    ): Action {
        return { type: 'REGISTER_NODE', id, schema, postId };
    },

    setValues( id: string, values: FieldValues ): Action {
        return { type: 'SET_VALUES', id, values };
    },

    setField( id: string, field: string, value: unknown ): Action {
        return { type: 'SET_FIELD', id, field, value };
    },

    // Thunks — load & save are async dispatchers
    loadValues( id: string ) {
        return async ( { dispatch, select }: { dispatch: typeof actions; select: typeof selectors } ) => {
            const schema = select.getSchema( id );
            if ( ! schema ) return;

            const postId = select.getPostId( id );
            const dataConfig = ( schema as PageSchema | MetaboxSchema ).data as DataConfig;

            try {
                const values = await loadData( dataConfig, postId );
                dispatch( { type: 'LOAD_SUCCESS', id, values } );
            } catch ( err ) {
                // Silently ignore — UI stays with empty defaults.
                console.warn( `UIX: failed to load data for ${ id }`, err );
            }
        };
    },

    saveValues( id: string ) {
        return async ( { dispatch, select }: { dispatch: typeof actions; select: typeof selectors } ) => {
            const schema = select.getSchema( id );
            if ( ! schema ) return;

            const values = select.getValues( id );
            const postId = select.getPostId( id );
            const dataConfig = ( schema as PageSchema | MetaboxSchema ).data as DataConfig;

            dispatch( { type: 'SAVE_START', id } );

            try {
                const saved = await saveData( dataConfig, values, postId );
                dispatch( { type: 'SAVE_SUCCESS', id, values: saved } );
            } catch ( err: unknown ) {
                const message = err instanceof Error ? err.message : 'Unknown error';
                dispatch( { type: 'SAVE_ERROR', id, error: message } );
            }
        };
    },
};

// ---------------------------------------------------------------------------
// Selectors
// ---------------------------------------------------------------------------

const selectors = {
    getSchema( state: StoreState, id: string ): AnyNode | undefined {
        return state.nodes[ id ]?.schema;
    },

    getValues( state: StoreState, id: string ): FieldValues {
        return state.nodes[ id ]?.values ?? {};
    },

    getFieldValue( state: StoreState, id: string, field: string ): unknown {
        return state.nodes[ id ]?.values[ field ];
    },

    getSaveStatus( state: StoreState, id: string ): SaveStatus {
        return state.nodes[ id ]?.saveStatus ?? 'idle';
    },

    getSaveError( state: StoreState, id: string ): string | null {
        return state.nodes[ id ]?.saveError ?? null;
    },

    isDirty( state: StoreState, id: string ): boolean {
        const node = state.nodes[ id ];
        if ( ! node ) return false;
        return JSON.stringify( node.values ) !== JSON.stringify( node.savedValues );
    },

    getPostId( state: StoreState, id: string ): number | undefined {
        return state.nodes[ id ]?.postId;
    },
};

// ---------------------------------------------------------------------------
// Store registration
// ---------------------------------------------------------------------------

export const STORE_NAME = 'uix';

const store = createReduxStore( STORE_NAME, {
    reducer,
    actions,
    selectors,
} );

register( store );

export { actions, selectors };
export type { StoreState };
