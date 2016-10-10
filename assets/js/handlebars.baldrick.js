/* Baldrick handlebars.js templating plugin */
(function($){
	var compiledTemplates	= {};
	$.fn.baldrick.registerhelper('handlebars', {
		bind	: function(triggers, defaults){
			var	templates = triggers.filter("[data-template-url]");
			if(templates.length){
				templates.each(function(){
					var trigger = $(this);
					//console.log(trigger.data());
					if(typeof compiledTemplates[trigger.data('templateUrl')] === 'undefined'){
						compiledTemplates[trigger.data('templateUrl')] = true;

						if(typeof(Storage)!=="undefined"){

							var cache, key;
							
							if(trigger.data('cacheLocal')){
								
								key = trigger.data('cacheLocal');
								
								cache = localStorage.getItem( 'handlebars_' + key );
							
							}else if(trigger.data('cacheSession')){

								key = trigger.data('cacheSession');

								cache = sessionStorage.getItem( 'handlebars_' + key );
							}

						}
						
						if(cache){
							compiledTemplates[trigger.data('templateUrl')] = Handlebars.compile(cache);
						}else{
							/*$.get(trigger.data('templateUrl'), function(data, ts, xhr){
								
								if(typeof(Storage)!=="undefined"){

									var key;
									
									if(trigger.data('cacheLocal')){
										
										key = trigger.data('cacheLocal');

										localStorage.setItem( 'handlebars_' + key, xhr.responseText );
									
									}else if(trigger.data('cacheSession')){
										
										key = trigger.data('cacheSession');

										sessionStorage.setItem( 'handlebars_' + key, xhr.responseText );
									}
								}

								compiledTemplates[trigger.data('templateUrl')] = Handlebars.compile(xhr.responseText);
							});*/
						}
					}
				});
			}

		},
		request			: function(obj, defaults){
			if(obj.params.trigger.data('templateUrl')){
				if( typeof compiledTemplates[obj.params.trigger.data('templateUrl')] === 'boolean' ){
					$.get(obj.params.trigger.data('templateUrl'), function(data, ts, xhr){
						compiledTemplates[obj.params.trigger.data('templateUrl')] = Handlebars.compile(xhr.responseText);
						obj.params.trigger.trigger(obj.params.event);
					});
					return false;
				}
			}
			return obj;
		},
		request_params	: function(request, defaults, params){
			if((params.trigger.data('templateUrl') || params.trigger.data('template')) && typeof Handlebars === 'object'){
				request.dataType = 'json';
				return request;
			}
		},
		filter			: function(opts, defaults){			

			if(opts.params.trigger.data('templateUrl')){
				if( typeof compiledTemplates[opts.params.trigger.data('templateUrl')] === 'function' ){
					opts.data = compiledTemplates[opts.params.trigger.data('templateUrl')](opts.data);
				}
			}else if(opts.params.trigger.data('template')){
				if( typeof compiledTemplates[opts.params.trigger.data('template')] === 'function' ){

					opts.data = compiledTemplates[opts.params.trigger.data('template')](opts.data);
				}else{
					if($(opts.params.trigger.data('template'))){
						compiledTemplates[opts.params.trigger.data('template')] = Handlebars.compile($(opts.params.trigger.data('template')).html());
						opts.data = compiledTemplates[opts.params.trigger.data('template')](opts.data);
					}
				}
			}

			return opts;
		}
	});

})(jQuery);


Handlebars.registerHelper("even", function(options) {
	var intval = options.data.index / 2;
	if( intval === Math.ceil( intval ) ){
		return options.fn(this);
	}else{
		return false;
	}

});
Handlebars.registerHelper("odd", function(options) {
	var intval = options.data.index / 2;
	if( intval === Math.ceil( intval ) ){
		return false;
	}else{
		return options.fn(this);
	}
});
Handlebars.registerHelper("json", function(context) {
	if( context && context._tab ){
		delete context._tab;
	}
	return JSON.stringify( context, null, 3 );
});
Handlebars.registerHelper(":node_point", function(context) {

	if( this._node_point ){
		var nodes = this._node_point.split('.'),
			node_point_record = nodes.join('][') + ']',
			node_path = node_point_record.replace( nodes[0] + ']', nodes[0] );

		return new Handlebars.SafeString( '<input type="hidden" name="' + node_path + '[_id]" value="' + nodes[nodes.length-1] + '"><input type="hidden" name="' + node_path + '[_node_point]" value="' + this._node_point + '">' );
	}

});

Handlebars.registerHelper(":name", function(context) {

	if( this._node_point ){
		var nodes = this._node_point.split('.'),
			node_point_record = nodes.join('][') + ']',
			node_path = node_point_record.replace( nodes[0] + ']', nodes[0] );

		return node_path;
	}

});

Handlebars.registerHelper('find', function(obj, field, options) {
	if( typeof obj === 'undefined' || typeof obj[field] === 'undefined' ){

		// check a nother level
		for( var part in obj ){

			if( typeof obj[part] !== 'undefined' && typeof obj[part][field] !== 'undefined' ){
				if( typeof obj[part][field] === 'string' && ( obj[part][field] === '' || obj[part][field] === null || obj[part][field] === false )  ){
					return options.inverse(this)
				}
				return options.fn(obj[part]);
			}
			if( obj[part] === field ){
				return options.fn(obj[part]);
			}
		}
		return options.inverse(this)
	}else{

		return options.fn(obj[field]);
	}

});
Handlebars.registerHelper("is_single", function(value, options) {
	if(Object.keys(value).length !== 1){
		return false;
	}else{
		return options.fn(this);
	}
});
Handlebars.registerHelper("script", function(options) {
	var atts = [];
	for( var att in options.hash ){
		atts.push( att + '="' + options.hash[att] + '"' );
	}

	return '<script ' + atts.join(' ') + '>' + options.fn(this) + '</script>';
});
Handlebars.registerHelper("is", function(value, options) {

	if( options.hash.value ){
		if(options.hash.value === '@key'){
			options.hash.value = options.data.key;
		}
		if(options.hash.value === value){
			return options.fn(this);
		}else{
			if(this[options.hash.value]){
				if(this[options.hash.value] === value){
					return options.fn(this);
				}
			}
			return options.inverse(this);
		}
	}
	if( options.hash.not ){
		if(options.hash.not === '@key'){
			options.hash.not = options.data.key;
		}
		if(options.hash.not !== value){
			return options.fn(this);
		}else{
			if(this[options.hash.not]){
				if(this[options.hash.not] !== value){
					return options.fn(this);
				}
			}
			return options.inverse(this);
		}
	}

});
Handlebars.registerHelper('load_partial', function(name, ctx, hash) {
	var ps = Handlebars.partials;
	if(typeof ps[name] !== 'function')
		ps[name] = Handlebars.compile(ps[name]);
	return ps[name](ctx, hash);
});
