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
 * <hr> separator. Mainly used for formatting
 *
 * @since 1.0.0
 */
class separator extends \uix\ui\control {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'separator';

	/**
	 * Return null alwasy since a separator should not show up as an input.
	 * @since 1.0.0
	 * @access public
	 * @return mixed $data
	 */
	public function get_data() {
		return null;
	}

	/**
	 * Returns the main input field for rendering
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string
	 */
	public function input() {

		return '<hr class="uix-separator" id="control-' . esc_attr( $this->id() ) . '" />';

	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function enqueue_active_assets() {
		parent::enqueue_active_assets();
		?>
		<style type="text/css">
		#control-<?php echo $this->id(); ?> {
			border-color: <?php echo $this->base_color(); ?>;
		}

		#
		<?php echo $this->id(); ?>
		span.uix-control-label {
			color: <?php echo $this->base_color(); ?>;
		}
		</style>
		<?php
	}

}
