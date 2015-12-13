<button type="button" class="button" data-add-node="locations">NODE!</button>
{{#each locations}}
	<div>
		{{:node_point}}
		<input type="text" name="{{:name}}[name]" value="{{name}}">
	</div>
{{/each}}
