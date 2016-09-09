<?php
/**
 * UIX Controls
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Implementaion of io.rangeSlider
 * @link https://github.com/IonDen/ion.rangeSlider
 *
 * @since 1.0.0
 */
class slider extends \uix\ui\control\text{

    /**
     * Define core UIX styles - override to register core ( common styles for uix type )
     *
     * @since 1.0.0
     * @access public
     */
    public function uix_styles() {
        parent::uix_styles();
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
     * @since 1.0.0
     * @access public
     */
    public function uix_scripts() {
        parent::uix_scripts();
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
     * @since  1.0.0
     * @access public
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