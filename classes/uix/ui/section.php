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
     * Define core page styles
     *
     * @since 1.0.0
     * @access public
     */
    public function uix_styles() {
        $pages_styles = array(
            'sections'    =>  $this->url . 'assets/css/uix-sections' . UIX_ASSET_DEBUG . '.css',
        );
        $this->styles( $pages_styles );
    }

    /**
     * Render the complete section
     *
     * @since 1.0.0
     * @access public
     */
    public function render() {
        
        if ( ! isset( $this->struct[ 'active' ] ) )
            $this->struct[ 'active' ] = 'true';

        echo '<div id="' . esc_attr( $this->id() ) . '" class="uix-section" aria-hidden="' . esc_attr( $this->struct[ 'active' ] ) . '">';

            $this->description();

            echo '<div class="uix-section-content">';

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
    public function render_section() {

        if ( ! empty( $this->struct[ 'template' ] ) ) {
            $this->render_template();
            return;
        }

        if ( empty( $this->child ) ) { return; }

        foreach ( $this->child as $control ) {
            $control->render();
        }
    }

    /**
     * checks if the current section is active
     *
     * @since 1.0.0
     * @access public
     */
    public function is_active() {
        return $this->parent->is_active();
    }

}