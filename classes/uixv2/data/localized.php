<?php
/**
 * UIX Data
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\data;

/**
 * localized data class
 * @package uixv2\data
 * @author  David Cramer
 */
abstract class localized extends data implements load{

    /**
     * object data to be saved
     *
     * @since 2.0.0
     * @access public
     * @var     mixed
     */
    public $data;

    /**
     * localizes data after enqueuing active assets
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        // load object data
        $config_object  = $this->load_data();

        /**
         * Filter config object
         *
         * @param array $config_object The object as retrieved from data
         * @param array $uix the UIX object
         */
        $config_object = apply_filters( 'uix_data-' . $this->type, $config_object, $this );

        $localize_data = array(
            'data'      => $config_object,
            'slug'      => $this->slug,
            'structure' => $this->struct
        );
        // localize data for this screen
        wp_localize_script( $this->type . '-admin', 'UIX', array( $this->slug => $localize_data ) );
    }

    /**
     * Define core localize UIX scripts
     *
     * @since 1.0.0
     *
     */
    public function uix_scripts() {
        // Initilize core scripts
        $core_scripts = array(
            'handlebars'    =>  $this->url . 'assets/js/handlebars.min-latest.js',
            'helpers'       =>  array(
                'src'           =>  $this->url . 'assets/js/uix-helpers' . $this->debug_scripts . '.js',
                'depends'       =>  array(
                    'jquery'
                )
            ),
            'admin'         =>  array(
                'src'           =>  $this->url . 'assets/js/uix-core' . $this->debug_scripts . '.js',
                'depends'       =>  array(
                    'jquery',
                    'handlebars'
                )               
            ),
            'modals'            =>  array(
                'src'           =>  $this->url . 'assets/js/uix-modals' . $this->debug_scripts . '.js',
                'depends'       =>  array(
                    'jquery'
                )               
            )
        );
        // push to activly register scripts
        $this->scripts( $core_scripts );
    }

    /**
     * Get data for the page
     *
     * @since 2.0.0
     *
     * @return mixed $data Requested data of the page
     */
    public function load_data(){

        // get and return config object
        return get_option( $this->store_key(), array() );    

    }

    /**
     * get a UIX config store key
     * @since 1.0.0
     *
     * @return string $store_key the defiuned option name for this UIX object
     */
    public function store_key(){

        if( !empty( $this->struct['store_key'] ) ){
            $store_key = $this->struct['store_key'];
        }else{
            $store_key = 'uix-' . $this->type . '-' . sanitize_text_field( $this->slug );
        }

        return $store_key;
    }

    /**
     * Return a saved object from part by points
     *
     * @since 1.0.0
     *
     * @return    string/array    the requested setting
     */
    public static function get_val( $path ) {

        $path = explode( '.', $path );
        $temp = null;
        $slug = array_shift( $path );

        $temp = $this->get_data();
        foreach ($path as $index => $value) {
            if( !isset( $temp[ $value ] ) ){
                return null;
            }
            $temp = $temp[ $value ];
        }

        return $temp;

    }
}