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
class section extends \uixv2\data\data {

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'section';


    /**
     * Sets the Sections to the current instance and registers it's Controls 
     *
     * @since 2.0.0
     * @see \uixv2\uix
     * @param array $objects object structure array
     */
    public function setup() {
        if( !empty( $this->struct['controls'] ) ){            
            foreach ( $this->struct['controls'] as $control_slug => $control_structure)
                $this->add_child( 'control\\' . $control_structure['type'], $control_slug, $control_structure );
        }
    }

    /**
     * Render the Section
     *
     * @since 2.0.0
     */
    public function render(){
        
        // load the section data 
        $data = $this->get_data();
        if( empty( $this->struct['active'] ) ){
            $hidden = 'true';
        }else{            
            $hidden = 'false';
        }
        echo '<div id="' . esc_attr( $this->slug . '-' . $this->parent->slug ) . '" class="uix-' . esc_attr( $this->parent->type ) . '-section" aria-hidden="' . esc_attr( $hidden ) . '">';
            echo '<div class="uix-' . esc_attr( $this->parent->type ) . '-section-content">';
                if( !empty( $this->struct['description'] ) ){
                    echo '<p class="description">' . esc_html( $this->struct['description'] ) . '</p>';
                }
                if( !empty( $this->struct['template'] ) ){
                    // tempalte
                    if( file_exists( $this->struct['template'] ) ){
                        include $this->struct['template'];
                    }else{
                        echo esc_html__( 'Template not found: ', 'text-domain' ) . $this->struct['template'];
                    }

                }elseif( !empty( $this->children ) ){
                    foreach ( $this->children as $control ) {
                        $control->render();
                    }                    
                }
                
            echo '</div>';
        echo '</div>';
    }

    /**
     * Get Data from all controls of this section
     *
     * @since 2.0.0
     * @see \uixv2\load
     * @param string $slug Slug of the section to get data for
     * @return array $data Array of sections data structured by the controls
     */
    public function get_data(){
        $data = array();
        if( !empty( $this->children ) ){
            foreach( $this->children as $control ) {
                $data[ $control->slug ] = $control->get_data();
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
     * checks if the current section is active
     *
     * @since 2.0.0
     */
    public function is_active(){
        return $this->parent->is_active();
    }

}