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
<p>The basic idea is that the UI is a representation of the config object. Change one and the other is affected.</p>

<p>It's not however true data-binding, but rather a series of triggers that allow one to update the other.</p>

<p>In order to tell the object what values, we need to have input fields. So the object is represented as a form, using the fields <code>name</code> attribute to define the context.</p>
<p>Below you'll see how the fields affect the config object.</p>

<p>
	Recalling a setting value in code <code>\namespace\ui\uix::get_setting('&lt;page_slug&gt;[ .tab_slug [.field_name ] ] ]');</code>
</p>
<p>
	So for this example to get the lastname you would do: <code>\namespace\ui\uix::get_setting('uix.general.lastname');</code><br>
	to get the whole tab as an array: <code>\namespace\ui\uix::get_setting('uix.general');</code><br>
	
</p>

<div style="float:left; Width:300px;min-height:200px;">
<h4>Some input Fields</h4>
	<code>name="firstname"</code>
	<input style="width:100%; "type="text" value="{{firstname}}" name="firstname" placeholder="firstname"><br>
	<code>name="lastname"</code>
	<input style="width:100%;" type="text" value="{{lastname}}" name="lastname" placeholder="lastname"><br>	
	<button style="width:100%;" type="button" class="button" data-live-sync="true">Update UI</button><br><br>
	<code>name="roles[]" value="admin"</code>
	<label style="display:block"><input data-live-sync="true" type="checkbox" value="admin" name="roles[]" {{#find roles "admin"}}checked="checked"{{/find}}> Admin</label><br>	
	<code>name="roles[]" value="editor"</code>	
	<label style="display:block"><input data-live-sync="true" type="checkbox" value="editor" name="roles[]" {{#find roles "editor"}}checked="checked"{{/find}}> Editor</label><br>	
	<code>name="roles[]" value="user"</code>	
	<label style="display:block"><input data-live-sync="true" type="checkbox" value="user" name="roles[]" {{#find roles "user"}}checked="checked"{{/find}}> User</label><br>	

</div>
<div style="float:left; Width:600px;min-height:200px;">
<h4>Current Config Object</h4>
<pre style="background: transparent;margin-left: 20px;"><code>{{json this}}</code></pre>
