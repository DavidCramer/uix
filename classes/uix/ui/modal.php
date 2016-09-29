<?php
/**
 * UIX Modal
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * Same as the Box type, however it renders a button control that loads the modal via a template
 * 
 * @package uix\ui
 * @author  David Cramer
 */
class modal extends panel{

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'modal';

    /**
     * footer object
     *
     * @since 1.0.0
     * @access public
     * @var      footer
     */
    public $footer;

    /**
     * modal template
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $templates = null;


    /**
     * Sets the controls data
     *
     * @since 1.0.0
     * @see \uix\uix
     * @access public
     */
    public function is_submitted(){
        $data = uix()->request_vars('post');
        return isset( $data['uixNonce_' . $this->id()] ) && wp_verify_nonce( $data['uixNonce_' . $this->id()], $this->id() );
    }


    /**
     * Render the footer template
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function set_footers(){

        if( !empty( $this->child ) ){
            foreach ($this->child as $child_slug=>$child){
                if ( in_array($child->type, array('footer') ) ){
                    $this->footer = $child;
                    $this->attributes['data-footer'] = '#' . $this->id() . '-footer-tmpl';
                }
            }
        }
    }
    /**
     * Sets the wrappers attributes
     *
     * @since 1.0.0
     * @access public
     */
    public function set_attributes(){

        $this->attributes += array(
            'data-modal'    =>  $this->id(),
            'data-content'  =>  '#' . $this->id() . '-tmpl',
            'data-margin'   =>  12,
            'data-element'  =>  'form',
            'class'         =>  'button',
        );

        $this->set_modal_config();

        if( !empty( $this->struct['description'] ) ){
            $this->attributes['data-title'] = $this->struct['description'];
            unset( $this->struct['description'] );
        }
        if( !empty( $this->struct['attributes'] ) )
            $this->attributes = array_merge( $this->attributes, $this->struct['attributes'] );


    }

    private function set_modal_config(){

        if( !empty( $this->struct['config'] ) ){
            $attributes = array();
            foreach( $this->struct['config'] as $att => $value )
                $attributes[ 'data-' . $att ] = $value;

            $this->attributes['data-config'] = json_encode( $attributes );
        }

    }

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        echo '<style>';
        echo 'h3#' . $this->id() . '_uixModalLable { background: ' . $this->base_color() . '; }';
        echo '#' . $this->id() . '_uixModal.uix-modal-wrap > .uix-modal-body:after {background: url(' . $this->url . 'assets/svg/loading.php?base_color=' . urlencode( str_replace('#', '', $this->base_color() ) ) . ') no-repeat center center;}';
        echo '</style>';

        parent::enqueue_active_assets();
    }

    /**
     * set assets
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     */
    public function set_assets() {

        $this->assets['script']['modals'] = array(
            'src' => $this->url . 'assets/js/modals' . UIX_ASSET_DEBUG . '.js',
            'deps' => array( 'baldrick' ),
        );
        $this->assets['style']['modals'] = $this->url . 'assets/css/modals' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();
    }


    /**
     * Render the Control
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function render(){

        $this->set_footers();

        add_action( 'admin_footer', array( $this, 'output_templates' ) );
        add_action( 'wp_footer', array( $this, 'output_templates' ) );

        $output = '<button ' . $this->build_attributes() . '>' . $this->struct['label'] . '</button>';

        $this->templates .= $this->render_modal_template();

        return $output;
    }

    /**
     * Render the Control
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function render_modal_template(){
        unset( $this->struct['label'] );
        $output = '<script type="text/html" id="' . esc_attr( $this->id() ) . '-tmpl">';
        $output .= wp_nonce_field( $this->id(), 'uixNonce_' . $this->id(), true, false );
        $output .= parent::render();
        $output .= '</script>';
        $output .= $this->render_footer_template();
        return $output;
    }


    /**
     * Render the footer template
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function render_footer_template(){
        $output = null;
        if ( !empty( $this->footer ) ){
            $output .= '<script type="text/html" id="' . esc_attr( $this->id() ) . '-footer-tmpl">';
            $output .= $this->footer->render();
            $output .= '</script>';
        }

        return $output;
    }

    /**
     * Render templates to page
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered box
     */
    public function output_templates(){
        echo $this->templates;
    }

}