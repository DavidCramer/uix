<?php
/**
 * Example modals page template 
 *
 * @package   templates
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      
 * @copyright 2016 David Cramer
 */
?>
<div class="uix-control-box">
	<div class="uix-control-bar">
		<span class="uix-control-bar-action left">
			<span class="dashicons dashicons-plus" 
				data-title="<?php echo esc_attr( 'Create Project' ); ?>"
				data-height="360"
				data-width="500"

				
				data-modal="project"
				data-template="project"
				data-focus="true"
				data-buttons="create"
				data-footer="conduitModalFooter"
				data-default='{"name":"untitled"}'
			></span>
		</span>
		<span class="uix-control-bar-content">Details</span>

	</div>

	<div class="uix-control-box-content">

		<div class="uix-grid">
			<div class="row">
				<div class="col-md-12">
				{{#unless project}}
					<p class="description">No Items</p>
				{{/unless}}
					{{#each project}}
									
						<div class="uix-control-bar item_{{_id}} hover {{#is @root/active_node value=_id}}active{{/is}}">
							<label class="uix-control-bar-action left">
								<span class="dashicons dashicons-edit"

									data-title="<?php echo esc_attr( 'Create Project' ); ?>"
									data-height="360"
									data-width="500"

									
									data-modal="{{_node_point}}"
									data-template="project"
									data-focus="true"
									data-buttons="save delete"
									data-footer="conduitModalFooter"

								></span>
								<input class="hidden" type="radio" name="active_node" data-live-sync="true" value="{{_id}}" {{#is @root/active_node value=_id}}checked="checked"{{/is}}>
							</label>						
							{{:node_point}}
							<span class="uix-control-bar-content">
								{{name}}
								<input type="hidden" name="{{:name}}" value="{{json this}}">
							</span>
							<span class="uix-control-bar-content">
							{{description}}
							</span>

							<span class="uix-control-bar-action right">
								<span class="dashicons dashicons-no" data-remove-element=".item_{{_id}}"></span>
							</span>
							

						</div>

					{{/each}}

				</div>
				
			<div class="clear"></div>
		</div>

	</div>

</div>

