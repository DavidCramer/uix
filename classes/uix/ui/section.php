<?php
/**
 * UIX section
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * A generic holder for multiple controls. this panel type does not handle saving, but forms part of the data object tree.
 *
 * @since 1.0.0
 * @see \uix\uix
 */
class section extends panel {

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'section';


    /**
     * Define core page style
     *
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {

        $this->assets['style']['sections']   =  $this->url . 'assets/css/uix-sections' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();
    }

    /**
     * Render the complete section
     *
     * @since 1.0.0
     * @access public
     */
    public function render(){

        if( !isset( $this->struct['active'] ) )
            $this->struct['active'] = 'true';

        echo '<div id="' . esc_attr( $this->id() ) . '" class="uix-section" aria-hidden="' . esc_attr( $this->struct['active'] ) . '">';

            $this->description();

            echo '<div class="uix-section-content">';

                $this->render_template();

                if( !empty( $this->child ) )
                    $this->render_section();
                
            echo '</div>';

        echo '</div>';

    }

    /**
     * Render the section body
     *
     * @since 1.0.0
     * @access public
     */
    public function render_section(){

        foreach ($this->child as $control)
            $control->render();

    }


    /**
     * checks if the current section is active
     *
     * @since 1.0.0
     * @access public
     */
    public function is_active(){
        return $this->parent->is_active();
    }

}