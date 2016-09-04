<?php
/**
 * UIX Metaboxes
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui\control;

/**
 * UIX Control class.
 *
 * @since 2.0.0
 */
class slider extends \uixv2\ui\control\text{

    /**
     * Define core UIX styles - override to register core ( common styles for uix type )
     *
     * @since 2.0.0
     *
     */
    public function uix_styles() {
        // Initilize core styles
        $styles = array(
            'slider-control'        => $this->url . 'assets/controls/slider/css/ion.rangeSlider' . $this->debug_scripts . '.css',
            'slider-control-theme'  => $this->url . 'assets/controls/slider/css/ion.rangeSlider.skinHTML5' . $this->debug_scripts . '.css',
        );
        // push to activly register styles
        $this->styles( $styles );

    }

    /**
     * Define core UIX scripts - override to register core ( common scripts for uix type )
     *
     * @since 2.0.0
     *
     */
    public function uix_scripts() {
        // Initilize core scripts
        $scripts = array(
            'slider-control'        => $this->url . 'assets/controls/slider/js/ion.rangeSlider' . $this->debug_scripts . '.js',
            'slider-control-init'   => array(
                "src"       => $this->url . 'assets/controls/slider/js/ion.rangeSlider.init' . $this->debug_scripts . '.js',
                "in_footer" => true
            )
        );
        // push to activly register scripts
        $this->scripts( $scripts );
    }

    /**
     * Gets the attributes for the control.
     *
     * @since  2.0.0
     * @access private
     * @param string $slug Slug of the control 
     * @return array
     */
    public function attributes() {

        $attributes = parent::attributes();
        $attributes['class']                        = 'uix-slider';
        $attributes['data-type']                    = 'single';
        $attributes['data-input-values-separator']  = ';';
        
        return $attributes;
    }

}