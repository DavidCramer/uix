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
            'slider-control'        => $this->url . 'assets/controls/slider/css/ion.rangeSlider' . UIX_ASSET_DEBUG . '.css',
            'slider-control-theme'  => $this->url . 'assets/controls/slider/css/ion.rangeSlider.skinHTML5' . UIX_ASSET_DEBUG . '.css',
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
            'slider-control'        => $this->url . 'assets/controls/slider/js/ion.rangeSlider' . UIX_ASSET_DEBUG . '.js',
            'slider-control-init'   => array(
                "src"       => $this->url . 'assets/controls/slider/js/ion.rangeSlider.init' . UIX_ASSET_DEBUG . '.js',
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
     */
    public function set_attributes() {

        parent::set_attributes();
        $this->attributes['class']                        = 'uix-slider';
        $this->attributes['data-type']                    = 'single';
        $this->attributes['data-input-values-separator']  = ';';

    }

}