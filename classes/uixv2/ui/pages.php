<?php
/**
 * UIX Pages
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * Pages class
 * @package uixv2\ui
 * @author  David Cramer
 */
class pages extends \uixv2\data\localized implements \uixv2\data\save{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access protected
     * @var      string
     */
    protected $type = 'page';

    /**
     * Holds the option screen prefix
     *
     * @since 2.0.0
     * @access protected
     * @var      array
     */
    protected $plugin_screen_hook_suffix = array();

    /**
     * Holds the current page slug
     *
     * @since 2.0.0
     * @access protected
     * @var      string
     */
    protected $current_page = null; 

    /**
     * setup actions and hooks to add settings pate and save settings
     *
     * @since 2.0.0
     */
    protected function actions() {
        // run parent actions ( keep 'admin_head' hook )
        parent::actions();
        // add settings page
        add_action( 'admin_menu', array( $this, 'add_settings_page' ), 9 );
        // save config
        add_action( 'wp_ajax_uix_' . $this->type . '_save_config', array( $this, 'save_config') );
    }

    /**
     * Define core page styles
     *
     * @since 2.0.0
     */
    public function uix_styles() {
        $pages_styles = array(
            'admin'    =>  $this->url . 'assets/css/admin' . $this->debug_styles . '.css',
            'icons'     =>  $this->url . 'assets/css/icons' . $this->debug_styles . '.css',         
            'grid'      =>  $this->url . 'assets/css/grid' . $this->debug_styles . '.css',
            'controls'  =>  $this->url . 'assets/css/controls' . $this->debug_styles . '.css',
        );
        $this->styles( $pages_styles );
    }

    /**
     * Handles the Ajax request to save a page config
     *
     * @uses "wp_ajax_uix_save_config" hook
     *
     * @since 2.0.0
     */
    public function save_config(){

        if( ! empty( $_POST[ 'config' ] ) ){

            $config = json_decode( stripslashes_deep( $_POST[ 'config' ] ), true );
            $page_slug = sanitize_text_field( $_POST['page_slug'] );
            if( !wp_verify_nonce( $_POST[ 'uix_setup_' . $page_slug ], $this->type ) ){
                wp_send_json_error( esc_html__( 'Could not verify nonce', 'text-domain' ) );
            }

            
            $page = $this->get( $page_slug );

            if( !empty( $page ) ){

                if( !empty( $_POST['params'] ) ){
                    $this->objects[ $page_slug ] = array_merge( $this->objects[ $page_slug ], $_POST['params'] );
                }

                $this->save_data( $page_slug, $config );

                wp_send_json_success();

            }
        }

    }

    /**
     * Save data for a page
     * @since 2.0.0
     * @param string $slug slug of the page
     * @param mixed $data Data to be saved for the page
     */
    public function save_data( $slug, $data ){
        
        /**
         * Filter config object
         *
         * @param array $config the config array to save
         * @param array $uix the uix config to be saved for
         */
        $data = apply_filters( 'uix_save_config-' . $this->type, $data, $this );
        $store_key = $this->store_key();

        // save object
        update_option( $store_key, $data );
        
    }


    /**
     * Get data for the page
     *
     * @since 2.0.0
     *
     * @param string $slug slug of the page
     * @return mixed $data Requested data of the page
     */
    public function get_data( $slug ){

        // get and return config object
        return get_option( $this->store_key(), array() );    

    }

    /**
     * Determin if a page is to be loaded and set it active
     * @since 2.0.0
     */
    protected function locate(){

        // check that the scrren object is valid to be safe.
        $screen = get_current_screen();
            
        if( empty( $screen ) || !is_object( $screen ) || !in_array( $screen->base, $this->plugin_screen_hook_suffix ) ){
            return null;
        }

        // get the page slug from base ID
        $this->current_page = array_search( $screen->base, $this->plugin_screen_hook_suffix );

        $this->set_active( $this->current_page );

    }

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 2.0.0
     */
    protected function enqueue_active_assets(){
        // enque active slugs assets
        foreach( (array) $this->active_objects as $slug => $object ){
            // enqueue stlyes and scripts
            $this->enqueue( $object['structure'], $this->type . '-' . $slug );
            // add tabs
            if( !empty( $object['structure']['tabs'] ) ){
                foreach( $object['structure']['tabs'] as $tab_id => $tab ){
                    $this->enqueue( $tab, $this->type . '-' . $tab_id );
                }
            }

        }
        // continue with parent enqueue of active assets
        parent::enqueue_active_assets();
    }

    /**
     * Add the settings page
     *
     * @since 2.0.0
     *
     * @uses "admin_menu" hook
     */
    public function add_settings_page(){

        if( empty( $this->struct[ 'page_title' ] ) || empty( $this->struct['menu_title'] ) ){
            continue;
        }

        $args = array(
            'capability'    => 'manage_options',
            'icon'          =>  null,
            'position'      => null
        );
        $args = array_merge( $args, $this->struct );

        if( !empty( $page['parent'] ) ){

            $this->plugin_screen_hook_suffix[ $this->slug ] = add_submenu_page(
                $args[ 'parent' ],
                $args[ 'page_title' ],
                $args[ 'menu_title' ],
                $args[ 'capability' ], 
                $this->type . '-' . $this->slug,
                array( $this, 'create_admin_page' )
            );

        }else{

            $this->plugin_screen_hook_suffix[ $this->slug ] = add_menu_page(
                $args[ 'page_title' ],
                $args[ 'menu_title' ],
                $args[ 'capability' ], 
                $this->type . '-' . $this->slug,
                array( $this, 'create_admin_page' ),
                $args[ 'icon' ],
                $args[ 'position' ]
            );
        }

    }
    
    /**
     * Settings page callback to render page
     *
     * @since 2.0.0
     * @uses "add_submenu_page" and "add_menu_page" functions
     */
    public function create_admin_page(){

        $this->render( $this->current_page );

    }

    /**
     * Render the page
     *
     * @since 2.0.0
     */
    public function render(){

        if( !empty( $this->struct['base_color'] ) ){
        ?><style type="text/css">
            .contextual-help-tabs .active {
                border-left: 6px solid <?php echo $this->struct['base_color']; ?> !important;
            }
            <?php if( !empty( $this->struct['tabs'] ) && count( $this->struct['tabs'] ) > 1 ){ ?>
            .wrap > h1.uix-title {
                box-shadow: 0 0px 13px 12px <?php echo $this->struct['base_color']; ?>, 11px 0 0 <?php echo $this->struct['base_color']; ?> inset;
            }
            <?php }else{ ?>
            .wrap > h1.uix-title {
                box-shadow: 0 0 2px rgba(0, 2, 0, 0.1),11px 0 0 <?php echo $this->struct['base_color']; ?> inset;
            }
            <?php } ?>
            .uix-modal-wrap .uix-modal-title > h3,
            .wrap .uix-title a.page-title-action:hover{
                background: <?php echo $this->struct['base_color']; ?>;
                border-color: <?php echo $this->struct['base_color']; ?>;
            }
            .wrap .uix-title a.page-title-action:focus{
                box-shadow: 0 0 2px <?php echo $this->struct['base_color']; ?>;
                border-color: <?php echo $this->struct['base_color']; ?>;
            }

        </style>
        <?php
        }       
        ?>
        <div class="wrap uix-item" data-uix="<?php echo esc_attr( $this->current_page ); ?>">
            <h1 class="uix-title"><?php esc_html_e( $this->struct['page_title'] , 'text-domain' ); ?>
                <?php if( !empty( $this->struct['version'] ) ){ ?><small><?php esc_html_e( $this->struct['version'], 'text-domain' ); ?></small><?php } ?>
                <?php if( !empty( $this->struct['save_button'] ) ){ ?>
                <a class="page-title-action" href="#save-object" data-save-object="true">
                    <span class="spinner uix-save-spinner"></span>
                    <?php esc_html_e( $this->struct['save_button'], 'text-domain' ); ?>
                </a>
                <?php } ?>
            </h1>
            <?php if( !empty( $this->struct['tabs'] ) ){ ?>
            <nav class="uix-sub-nav" <?php if( count( $this->struct['tabs'] ) === 1 ){ ?>style="display:none;"<?php } ?>>
                <?php foreach( (array) $this->struct['tabs'] as $tab_slug => $tab ){ ?><a data-tab="<?php echo esc_attr( $tab_slug ); ?>" href="#<?php echo esc_attr( $tab_slug ) ?>"><?php echo esc_html( $tab['menu_title'] ); ?></a><?php } ?>
            </nav>
            <?php }

            wp_nonce_field( $this->type, 'uix_setup_' . $this->current_page );

            if( !empty( $this->struct['tabs'] ) ){
                foreach( (array) $this->struct['tabs'] as $tab_slug => $tab ){ ?>
                    <div class="uix-tab-canvas" data-app="<?php echo esc_attr( $tab_slug ); ?>"></div>
                    <script type="text/html" data-template="<?php echo esc_attr( $tab_slug ); ?>">
                        <?php 
                            if( !empty( $tab['page_title'] ) ){ echo '<h4>' . $tab['page_title']; }
                            if( !empty( $tab['page_description'] ) ){ ?> <small><?php echo $tab['page_description']; ?></small> <?php } 
                            if( !empty( $tab['page_title'] ) ){ echo '</h4>'; }
                            // include this tabs template
                            if( !empty( $tab['template'] ) && file_exists( $tab['template'] ) ){
                                include $tab['template'];
                            }else{
                                echo esc_html__( 'Template not found: ', 'text-domain' ) . $tab['page_title'];
                            }
                        ?>
                    </script>
                    <?php if( !empty( $tab['partials'] ) ){
                        foreach( $tab['partials'] as $partial_id => $partial ){
                            ?>
                            <script type="text/html" id="__partial_<?php echo esc_attr( $partial_id ); ?>" data-handlebars-partial="<?php echo esc_attr( $partial_id ); ?>">
                                <?php
                                    // include this tabs template
                                    if( !empty( $partial ) && file_exists( $partial ) ){
                                        include $partial;
                                    }else{
                                        echo esc_html__( 'Partial Template not found: ', 'text-domain' ) . $partial_id;
                                    }
                                ?>
                            </script>
                            <?php
                        }
                    }
                }
            }else{
                if( !empty( $this->struct['template'] ) && file_exists( $this->struct['template'] ) ){
                    include $this->struct['template'];
                }
            }
            if( !empty( $this->struct['modals'] ) ){
                foreach( $this->struct['modals'] as $modal_id => $modal ){
                    ?>
                    <script type="text/html" id="__modal_<?php echo esc_attr( $modal_id ); ?>" data-handlebars-partial="<?php echo esc_attr( $modal_id ); ?>">
                        <?php
                            // include this tabs template
                            if( !empty( $modal ) && file_exists( $modal ) ){
                                include $modal;
                            }else{
                                echo esc_html__( 'Modal Template not found: ', 'text-domain' ) . $modal_id;
                            }
                        ?>
                    </script>
                    <?php
                }
            }
            ?>          
        </div>

        <script type="text/html" data-template="__notice">
        <div class="{{#if success}}updated{{else}}error{{/if}} notice uix-notice is-dismissible">
            <p>{{{data}}}</p>
            <button class="notice-dismiss" type="button">
                <span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'text-domain' ); ?></span>
            </button>
        </div>
        </script>
        <script type="text/html" id="__partial_save">       
            <button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="save" 
                {{#if __callback}}data-callback="{{__callback}}"{{/if}}
                {{#if __before}}data-before="{{__before}}"{{/if}}
            >
                <?php esc_html_e( 'Save Changes', 'text-domain' ); ?>
            </button>
        </script>
        <script type="text/html" id="__partial_create">
            <button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="add" 
                {{#if __callback}}data-callback="{{__callback}}"{{/if}}
                {{#if __before}}data-before="{{__before}}"{{/if}}
            >
                <?php esc_html_e( 'Create', 'text-domain' ); ?>
            </button>
        </script>
        <script type="text/html" id="__partial_delete">
            <button style="float:left;" class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="delete" 
                {{#if __callback}}data-callback="{{__callback}}"{{/if}}
                {{#if __before}}data-before="{{__before}}"{{/if}}
            >
                <?php esc_html_e( 'Remove', 'text-domain' ); ?>
            </button>
        </script>
        <?php
    }
    
}