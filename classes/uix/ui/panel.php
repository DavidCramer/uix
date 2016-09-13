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
     * Define core panel styles ans scripts
     *
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {

        $this->assets['script']['panel']     =  $this->url . 'assets/js/uix-panel' . UIX_ASSET_DEBUG . '.js';
        $this->assets['style']['panel']   =  $this->url . 'assets/css/uix-panel' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();

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

        echo '<div id="' . esc_attr( $this->id() ) . '" class="uix-' . esc_attr( $this->type ) . '-inside ' . esc_attr( $this->wrapper_class_names() ) . '">';
            // render a lable
            $this->label();
            // render a desciption
            $this->description();
            // render navigation tabs
            $this->navigation();
            // render the section wrapper
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

    /**
     * Render the panels navigation tabs
     *
     * @since 1.0.0
     * @access public
     */
    public function navigation(){
        if( count( $this->child ) <= 1 ){ return; }

        echo '<ul class="uix-' . esc_attr( $this->type ) . '-tabs uix-panel-tabs">';
        $active = 'true';
        foreach( $this->child as $child ){

            if( $child->type === 'help' ){ continue; }

            $this->tab_label( $child, $active );

            $active = 'false';
        }
        echo '</ul>';
    }

    /**
     * Render the tabs label
     *
     * @since 1.0.0
     * @param object $child Child object to render tab for.
     * @param string $active Set the tabactive or not.
     * @access private
     */
    private function tab_label( $child, $active ){

        $label = esc_html( $child->struct['label'] );

        if( !empty( $child->struct['icon'] ) )
            $label = '<i class="dashicons ' . $child->struct['icon'] . '"></i><span class="label">' . esc_html( $child->struct['label'] ) . '</span>';

        echo '<li aria-selected="' . esc_attr( $active ) . '">';
        echo '<a href="#' . esc_attr( $child->id() ) . '" data-parent="' . esc_attr( $this->id() ) . '" class="uix-tab-trigger">' . $label . '</a>';
        echo '</li>';
    }

    /**
     * Render the panels label
     *
     * @since 1.0.0
     * @access public
     */
    public function label(){
        if( !empty( $this->struct['label'] ) )
            echo '<div class="uix-' . esc_attr( $this->type ) . '-heading"><h3 class="uix-' . esc_attr( $this->type ) . '-title">' . esc_html( $this->struct['label'] ) . '</h3></div>';
    }

    /**
     * Render the panels Description
     *
     * @since 1.0.0
     * @access public
     */
    public function description(){
        if( !empty( $this->struct['description'] ) )
            echo '<div class="uix-' . esc_attr( $this->type ) . '-heading"><p class="uix-' . esc_attr( $this->type ) . '-subtitle description">' . esc_html( $this->struct['description'] ) . '</p></div>';
        
    }

    /**
     * Render a template based object
     *
     * @since 1.0.0
     * @access public
     */
    public function render_template(){
        // tempalte
        if( file_exists( $this->struct['template'] ) ){
            include $this->struct['template'];
        }else{
            echo esc_html__( 'Template not found: ', 'text-domain' ) . $this->struct['template'];
        }
    }


    /**
     * Returns the class names for the tab wrapper
     *
     * @since 1.0.0
     * @access public
     */
    public function wrapper_class_names(){

        $wrapper_class_names = array(
            'uix-panel-inside'
        );

        if( count( $this->child ) > 1 )
            $wrapper_class_names[] = 'uix-has-tabs';

        if( !empty( $this->struct['top_tabs'] ) )
            $wrapper_class_names[] = 'uix-top-tabs';

        return implode( ' ', $wrapper_class_names );
    }
}