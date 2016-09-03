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
 * UIX sections class.
 *
 * @since 2.0.0
 * @see \uixv2\uix
 */
class sections extends uix implements \uixv2\data\save,\uixv2\data\load{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access protected
     * @var      string
     */
    protected $type = 'section';


    /**
     * Sets the Sections to the current instance and registers it's Controls 
     *
     * @since 2.0.0
     * @see \uixv2\uix
     * @param array $objects object structure array
     */
    public function set_objects( array $objects ) {
        
        // do parent
        parent::set_objects( $objects );

        foreach( $this->objects as $object_id => &$object ){
            
            if( empty( $object['controls'] ) ){ continue; }

            foreach( $object['controls'] as $control_slug => &$control ){
                // set the section id for the control
                $control['section'] = $object_id;

                if( !isset( $control['type'] ) )
                    $control['type'] = 'text'; // default to text control.
                // load the control structure
                uixv2()->load( 'control\\' . $control['type'], array( $control_slug => $control ) );
            }
        }        
    }

    /**
     * Render the Section
     *
     * @since 2.0.0
     * @param array $slug Section slug to be rendered
     */
    public function render( $slug ){
        
        $section = $this->get( $slug );

        // load the section data 
        $data = $this->get_data( $slug );

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
                        if( isset( uixv2()->ui->control[ $control['type'] ] ) ){
                            // render the control
                            uixv2()->ui->control[ $control['type'] ]->render( $control_slug );
                        }
                    }                    
                }
                
            echo '</div>';
        echo '</div>';
    }


    /**
     * Get the Sections data store key ( index )
     * @since 1.0.0
     * @see \uixv2\data
     * @return string $store_key the sanitized store key
     */
    public function store_key( $slug ){
        return sanitize_key( $slug );
    }    


    /**
     * Get Data from all controls of this section
     *
     * @since 2.0.0
     * @see \uixv2\load
     * @param string $slug Slug of the section to get data for
     * @return array $data Array of sections data structured by the controls
     */
    public function get_data( $slug ){
        $section = $this->get( $slug );
        $data = array();
        if( !empty( $section['controls'] ) ){
            foreach( $section['controls'] as $control_id => $control) {
                $data[ $control_id ] = uixv2()->ui->control[ $control['type'] ]->get_data( $control_id );
            }
        }
        
        return $data;
    }


    /**
     * Save Section data to the section controls
     *
     * @since 2.0.0
     * @param string $slug slug of the section
     * @param mixed $data Data to be saved for the Section
     * @see \uixv2\save
     */
    public function save_data( $slug, $data ){
        
        $section = $this->get( $slug );
        if( empty( $section['controls'] ) ){ return; }

        foreach( $section['controls'] as $control_id => $control ) {
            $value = null;
            if( isset( $data[ $control_id ] ) ){
                $value = $data[ $control_id ];
            }
            uixv2()->ui->control[ $control['type'] ]->save_data( $control_id, $value );
        }
    }    

    /**
     * Sets a Section and its Controls to Active
     *
     * @since 2.0.0
     * @param $slug     Slug of the section to set as active
     */
    public function set_active( $slug ){
        if( !in_array( $slug, $this->active_slugs ) ){
            $section = $this->get( $slug );
            if( !empty( $section['controls'] ) ){
                foreach( $section['controls'] as $control_id => $control ) {
                    uixv2()->ui->control[ $control['type'] ]->set_active( $control_id );
                }
            }
            $this->active_slugs[] = $slug;
        }
    }

}