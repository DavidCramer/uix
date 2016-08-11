<?php
/**
 * modal config Template 
 *
 * @package   /uix/
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 David Cramer
 */
?>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="name"><?php esc_html_e( 'Name' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" value="{{name}}" name="name" id="name" required="required">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="description"><?php esc_html_e( 'Description' ); ?></label>
			</th>
			<td>
				<textarea style="width: 100%; height: 130px;" name="description" id="description">{{description}}</textarea>
			</td>
		</tr>
	</tbody>
</table>
