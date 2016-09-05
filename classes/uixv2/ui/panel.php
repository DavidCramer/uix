<?php
/**
 * UIX Panel
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * UIX Page class
 * @package uixv2\ui
 * @author  David Cramer
 */
class panel extends uix{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'panel';

    /**
     * Registeres the panels Sections
     *
     * @since 2.0.0
     * @see \uixv2\uix
     * @access public
     */
    public function setup() {
        if( !empty( $this->struct['sections'] ) ){            
            foreach ( $this->struct['sections'] as $section_slug => $section_structure)
                $this->section( $section_slug, $section_structure );
        }
    }

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        if( !empty( $this->struct['base_color'] ) ){
            $text_color = "#fff";
            if( !empty( $this->struct['base_text_color'] ) ){
                $text_color = $this->struct['base_text_color'];
            }
        ?><style type="text/css">
        #<?php echo 'uix-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ); ?> .uix-panel-tabs li[aria-selected="true"] a {
            box-shadow: 3px 0 0 <?php echo $this->struct['base_color']; ?> inset;
        }
        </style>
        <?php
        }
    }


    /**
     * Define core panel styles
     *
     * @since 2.0.0
     * @access public
     */
    public function uix_styles() {
        $styles = array(
            'panel'    =>  $this->url . 'assets/css/uix-panel' . $this->debug_styles . '.css',
        );
        $this->styles( $styles );
    }

    /**
     * set metabox scripts
     *
     * @since 2.0.0
     * @see \uixv2\ui\uix
     * @access public
     */
    public function uix_scripts() {
        $scripts = array(
            'panel'        =>  $this->url . 'assets/js/uix-panel' . $this->debug_scripts . '.js'
        );
        $this->scripts( $scripts );
    }

    /**
     * Render the panel
     *
     * @since 2.0.0
     * @access public
     */
    public function render(){
        
        if( empty( $this->child ) ){ return; }

        if( count( $this->child ) > 1 ){
            echo '<div id="uix-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ) . '" class="uix-panel-inside uix-has-tabs">';
                echo '<ul class="uix-panel-tabs">';
                $active = 'true';
                foreach( $this->child as $section ){
                    
                    $label = esc_html( $section->struct['label'] );

                    if( !empty( $section->struct['icon'] ) ){
                        $label = '<i class="dashicons ' . $section->struct['icon'] . '"></i><span class="label">' . esc_html( $section->struct['label'] ) . '</span>';
                    }
                    echo '<li aria-selected="' . esc_attr( $active ) . '">';
                        echo '<a href="#' . esc_attr( $section->slug . '-' . $this->slug ) . '" data-parent="uix-' . esc_attr( $this->type ) . '-' . esc_attr( $this->slug ) . '" class="uix-tab-trigger">' . $label . '</a>';
                    echo '</li>';

                    $active = 'false';
                }
                echo '</ul>';
        }else{
            echo '<div class="uix-panel">';
        }

            echo '<div class="uix-sections">';
                $hidden = 'false';
                foreach( $this->child as $section ){
                    $section->struct['active'] = $hidden;
                    $section->render();
                    $hidden = 'true';
                }
            echo '</div>';
        echo '</div>';
    }

    
}