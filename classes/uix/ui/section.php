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

        $this->assets['style']['sections']   =  $this->url . 'assets/css/sections' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();
    }

    /**
     * Render the complete section
     *
     * @since 1.0.0
     * @access public
     * @return string|null HTML of rendered notice
     */
    public function render(){

        $output = null;

        if( !isset( $this->struct['active'] ) )
            $this->struct['active'] = 'true';

        $output .= '<div id="' . esc_attr( $this->id() ) . '" class="uix-section" aria-hidden="' . esc_attr( $this->struct['active'] ) . '">';

            $output .= $this->description();

            $output .='<div class="uix-section-content">';

                $output .= $this->render_template();

                if( !empty( $this->child ) )
                    $output .= $this->render_section();

            $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Render the section body
     *
     * @since 1.0.0
     * @access public
     * @return string|null
     */
    public function render_section(){
        $output = null;
        foreach ($this->child as $control)
            $output .= $control->render();

        return $output;
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