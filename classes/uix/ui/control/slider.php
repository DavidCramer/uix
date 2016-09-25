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
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'slider';

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
     * Sets styling colors
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){

        parent::enqueue_active_assets();
        echo '<style type="text/css">';

        echo '.' . $this->id() . ' .irs-grid-pol {background: ' . $this->struct['base_color'] . ';}';
        echo '.' . $this->id() . ' .irs-bar {border-top: 1px solid ' . $this->struct['base_color'] . ';border-bottom: 1px solid ' . $this->struct['base_color'] . ';background: ' . $this->struct['base_color'] . ';}';
        echo '.' . $this->id() . ' .irs-bar-edge {border: 1px solid ' . $this->struct['base_color'] . ';background: ' . $this->struct['base_color'] . ';}';
        echo '.' . $this->id() . ' .irs-from, .' . $this->id() . ' .irs-to, .' . $this->id() . ' .irs-single {background: ' . $this->struct['base_color'] . ';}';

        echo '</style>';

    }
    /**
     * Gets the attributes for the control.
     *
     * @since  1.0.0
     * @access public
     */
    public function set_attributes() {

        parent::set_attributes();

        $this->attributes['data-type']                    = 'single';
        $this->attributes['data-input-values-separator']  = ';';
        $this->attributes['class']                        = 'uix-slider';

        if( !empty( $this->struct['config'] ) )
            $this->set_config();
    }

    /**
     * Gets the slider config for the control.
     * @link http://ionden.com/a/plugins/ion.rangeSlider/en.html
     * @since  1.0.0
     * @access public
     */
    public function set_config() {

        foreach( $this->struct['config'] as $key=>$setting )
            $this->attributes['data-' . $key ] = $setting;

    }

}