<?php
/**
 * Example page template 
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
			<span class="dashicons dashicons-plus" data-add-node="item"></span>
		</span>
		<span class="uix-control-bar-content">UIX Control Bar</span>

	</div>

	<div class="uix-control-box-content">

		<div class="uix-grid">
			<div class="row">
				<div class="col-sm-6">

					{{#each item}}
									
						<div class="uix-control-bar item_{{_id}} hover {{#is @root/active_node value=_id}}active{{/is}}">
							{{:node_point}}
							<span class="uix-control-bar-content">
								ID: {{_id}}
								{{#is @root/active_node not=_id}}
									{{#if parts}}
										<input type="hidden" name="{{:name}}[parts]" value="{{json parts}}">
									{{/if}}
								{{/is}}
							</span>

							<label class="uix-control-bar-action right">
								<span class="dashicons dashicons-arrow-right"></span>
								<input class="hidden" type="radio" name="active_node" data-live-sync="true" value="{{_id}}" {{#is @root/active_node value=_id}}checked="checked"{{/is}}>
							</label>

							<span class="uix-control-bar-action right">
								<span class="dashicons dashicons-no" data-remove-element=".item_{{_id}}"></span>
							</span>

						</div>

					{{/each}}

				</div>
				<div class="col-sm-6">

				{{#find item active_node}}

					<div class="uix-control-box">
						<div class="uix-control-bar">
							<span class="uix-control-bar-action uix-icon left">
								<span class="dashicons dashicons-plus" data-add-node="{{_node_point}}.parts"></span>
							</span>
							<span class="uix-control-bar-action uix-icon left">
								<span class="dashicons dashicons-admin-generic"></span>
							</span>
							<span class="uix-control-bar-content">View Node: {{_id}}</span>
							<span class="uix-control-bar-action uix-button right">
								<button type="button" class="button">Button</button>
							</span>

							<span class="uix-control-bar-action uix-button right">
								<button type="button" class="button button-small">Small</button>
							</span>

						</div>
	
						<div class="uix-control-box-content">

							{{#each parts}}
											
								<div class="uix-control-bar item_{{_id}}">
									{{:node_point}}				
									<label class="uix-control-bar-action left">
										<input type="checkbox" value="1">
									</label>
									<label class="uix-control-bar-action left">
										<input type="radio" value="1" name="pick">
									</label>
																				
									<input type="text" class="" value="{{_id}}">
									
									<span class="uix-control-bar-action right">
										<span class="dashicons dashicons-no" data-remove-element=".item_{{_id}}"></span>
									</span>
								</div>

							{{/each}}

						</div>

					</div>

					{{/find}}

				</div>

			</div>
			<div class="clear"></div>
		</div>

	</div>

	<div class="uix-control-box">
		<div class="uix-control-bar">
			<span class="uix-control-bar-content">
				This is a text line
			</span>

			<span class="uix-control-bar-action right">
				<span class="dashicons dashicons-plus"></span>
			</span>
			<span class="uix-control-bar-action right">
				<span class="dashicons dashicons-no"></span>
			</span>

		</div>

	</div>

</div>
