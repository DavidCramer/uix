<?php
/**
 * UIX Panel
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * UIX panel. a holder to contain sections. a panel with multiple sections creates a tabbed interface to switch between sections areas.
 * 
 * @package uix\ui
 * @author  David Cramer
 */
class panel extends \uix\data\data{

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'panel';

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        ?><style type="text/css">
        #<?php echo $this->id(); ?> > .uix-panel-tabs > li[aria-selected="true"] a {
            box-shadow: 3px 0 0 <?php echo $this->base_color(); ?> inset;
        }
        #<?php echo $this->id(); ?>.uix-top-tabs > .uix-panel-tabs > li[aria-selected="true"] a {
            box-shadow: 0 3px 0 <?php echo $this->base_color(); ?> inset;
        }
        
        </style>
        <?php
    }


    /**
     * Define core panel styles
     *
     * @since 1.0.0
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
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     */
    public function uix_scripts() {
        $scripts = array(
            'panel'        =>  $this->url . 'assets/js/uix-panel' . $this->debug_scripts . '.js'
        );
        $this->scripts( $scripts );
    }

    /**
     * Get Data from all controls of this section
     *
     * @since 1.0.0
     * @see \uix\load
     * @return array Array of sections data structured by the controls
     */
    public function get_data(){
        $data = array();
        if( !empty( $this->child ) ){
            foreach( $this->child as $child ) {
                $data[ $child->slug ] = $child->get_data();
            }
        }

        return $data;
    }

    /**
     * Sets the data for all children
     *
     * @since 1.0.0
     * @access public
     */    
    public function set_data( $data ){
        if( empty( $this->child ) ){ return; }

        foreach( $this->child as $child ){
            if( isset( $data[ $child->slug ] ) )
                $child->set_data( $data[ $child->slug ] );
        }

    }

    /**
     * Render the panel
     *
     * @since 1.0.0
     * @access public
     */
    public function render(){
        
        if( empty( $this->child ) ){ return; }
        $tabs_class = '';
        if( count( $this->child ) > 1 )
            $tabs_class = ' uix-has-tabs';
        if( !empty( $this->struct['top_tabs'] ) )
            $tabs_class .= ' uix-top-tabs';

        echo '<div id="' . esc_attr( $this->id() ) . '" class="uix-' . esc_attr( $this->type ) . '-inside uix-panel-inside ' . $tabs_class . '">';

        if( !empty( $this->struct['label'] ) )
            echo '<div class="uix-panel-heading"><h3 class="uix-panel-title">' . esc_html( $this->struct['label'] ) . '</h3></div>';

        if( !empty( $this->struct['description'] ) )
            echo '<div class="uix-panel-heading"><p class="uix-panel-subtitle description">' . esc_html( $this->struct['description'] ) . '</p></div>';
        
        if( count( $this->child ) > 1 ){
                echo '<ul class="uix-' . esc_attr( $this->type ) . '-tabs uix-panel-tabs">';
                $active = 'true';
                foreach( $this->child as $child ){
                    
                    $label = esc_html( $child->struct['label'] );

                    if( !empty( $child->struct['icon'] ) ){
                        $label = '<i class="dashicons ' . $child->struct['icon'] . '"></i><span class="label">' . esc_html( $child->struct['label'] ) . '</span>';
                    }
                    echo '<li aria-selected="' . esc_attr( $active ) . '">';
                        echo '<a href="#' . esc_attr( $child->id() ) . '" data-parent="' . esc_attr( $this->id() ) . '" class="uix-tab-trigger">' . $label . '</a>';
                    echo '</li>';

                    $active = 'false';
                }
                echo '</ul>';
        }

            echo '<div class="uix-' . esc_attr( $this->type ) . '-sections uix-sections">';
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