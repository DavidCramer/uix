<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="example">Example Setting</label>
			</th>
			<td>
				<input type="text" class="regular-text" value="{{example}}" id="example" name="example">
			</td>
		</tr>
	</tbody>
</table>
{{#script}}
jQuery( function( $ ){
var elements = ['user:id', 'user:name', 'user:email', 'spinn', 'niii'];
$('#example').textcomplete([
    { // html
        match: /{(\w*)$/,
        search: function (term, callback) {
            callback($.map(elements, function (element) {
                return element.indexOf(term) === 0 ? element : null;
            }));
        },
        index: 1,
	    replace: function (word) {
	        return '{' + word + '} ';
	    }
    },
    { // html
        match: /%(\w*)$/,
        search: function (term, callback) {
            callback($.map(elements, function (element) {
                return element.indexOf(term) === 0 ? element : null;
            }));
        },
        index: 1,
	    replace: function (word) {
	        return '%' + word + '% ';
	    }
    }   
]);
});

{{/script}}