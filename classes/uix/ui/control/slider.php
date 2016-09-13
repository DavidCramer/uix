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
    public function set_assets() {

        // Initilize core styles
        $this->assets['style']['slider-control']        = $this->url . 'assets/controls/slider/css/ion.rangeSlider' . UIX_ASSET_DEBUG . '.css';
        $this->assets['style']['slider-control-theme']  = $this->url . 'assets/controls/slider/css/ion.rangeSlider.skinHTML5' . UIX_ASSET_DEBUG . '.css';


        // Initilize core scripts
        $this->assets['script']['slider-control']        = $this->url . 'assets/controls/slider/js/ion.rangeSlider' . UIX_ASSET_DEBUG . '.js';
        $this->assets['script']['slider-control-init']  = array(
            "src"       => $this->url . 'assets/controls/slider/js/ion.rangeSlider.init' . UIX_ASSET_DEBUG . '.js',
            "in_footer" => true
        );

        parent::set_assets();
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