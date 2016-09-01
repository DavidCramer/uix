<?php
/**
 * UIX section
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * UIX secstions class.
 *
 * @since 2.0.0
 */
class sections extends uix implements \uixv2\data\save,\uixv2\data\load{

    /**
     * The type of object
     *
     * @since 1.0.0
     *
     * @var      string
     */
    protected $type = 'section';

    /**
     * controlls for this section
     *
     * @since 1.0.0
     *
     * @var      uix/controls
     */
    protected $controls;

    /**
     * Register the UIX objects
     *
     * @since 1.0.0
     *
     * @param array $objects object structure array
     */
    public function set_objects( array $objects ) {
        
        parent::set_objects( $objects );

        foreach( $this->objects as $object_id => &$object ){
            
            if( empty( $object['controls'] ) ){ continue; }

            foreach( $object['controls'] as $control_slug => &$control ){
                
                $control['section'] = $object_id;
                if( $control['type'] ){
                    uixv2()->load( 'control\\' . $control['type'], array( $control_slug => $control ) );
                }else{
                    uixv2()->load( 'control', array( $control_slug => $control ) );
                }

            }
        }
        
        
        
    }

    public function render( $slug ){
        
        $section = $this->get( $slug );

        if( empty( $section['controls'] ) && empty( $section['template'] ) ){ continue; }
        
        if( empty( $section['active'] ) ){
            $section['active'] = 'true';
        }else{
            $section['active'] = 'false';
        }
        echo '<div id="' . esc_attr( $slug ) . '" class="uix-metabox-section" aria-hidden="' . esc_attr( $section['active'] ) . '">';
            echo '<div class="uix-metabox-section-content">';
                if( !empty( $section['description'] ) ){
                    echo '<p class="description">' . esc_html( $section['description'] ) . '</p>';
                }
                if( !empty( $section['template'] ) ){
                    // tempalte
                    if( file_exists( $section['template'] ) ){
                        include $section['template'];
                    }else{
                        echo esc_html__( 'Template not found: ', 'text-domain' ) . $section['template'];
                    }
                }else{
                    foreach ( $section['controls'] as $control_slug => $control ) {
                        if( isset( uixv2()->control[ $control['type'] ] ) ){
                            uixv2()->control[ $control['type'] ]->render( $control_slug );
                        }
                    }
                    // controls                    
                    
                }
                
            echo '</div>';
        echo '</div>';
    }


    /**
     * get the objects data store key
     * @since 1.0.0
     *
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key( $slug ){
        return sanitize_key( $slug );
    }    


    /**
     * Get data
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @return array of data
     */
    public function get_data( $slug ){

    }

    /**
     * save data
     *
     * @since 1.0.0
     * @param string $slug slug of the object
     * @param array $data array of data to be saved
     *
     * @return bool true on successful save
     */
    public function save_data( $slug, $data ){
        
        $section = $this->get( $slug );
        if( empty( $section['controls'] ) ){ return; }

        foreach( $section['controls'] as $control_id => $control ) {
            $value = null;
            if( isset( $data[ $control_id ] ) ){
                $value = $data[ $control_id ];
            }
            uixv2()->control[ $control['type'] ]->save_data( $control_id, $value );
        }
    }    

}