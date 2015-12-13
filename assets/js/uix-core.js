var conduitApp = {},
	coduitTemplates = {},
	conduitRegisterApps,
	conduitGetData;	

!( jQuery( function($){


	$.fn.conduitTrigger = function( obj ){
		var defaults = {
			method	:	'GET'
		};

		$.extend(true, defaults, obj);

		return this;
	}

	$.fn.getObject = function(){
		var element = $(this);

		var fields   = element.find('[name]'),
		obj         = {},
		arraynames   = {};
		for( var v = 0; v < fields.length; v++){
			var field     = $( fields[v] ),
			name    = field.prop('name').replace(/\]/gi,'').split('['),
			value     = field.val(),
			lineconf  = {};

			if( field.is(':radio') || field.is(':checkbox') ){
				if( !field.is(':checked') ){
					continue;
				}
			}

			for(var i = name.length-1; i >= 0; i--){
				var nestname = name[i];
				if( typeof nestname === 'undefined' ){
					nestname = '';
				}
				if(nestname.length === 0){
					lineconf = [];
					if( typeof arraynames[name[i-1]] === 'undefined'){
						arraynames[name[i-1]] = 0;
					}else{
						arraynames[name[i-1]] += 1;
					}
					nestname = arraynames[name[i-1]];
				}
				if(i === name.length-1){
					if( value ){
						if( value === 'true' ){
							value = true;
						}else if( value === 'false' ){
							value = false;
						}else if( !isNaN( parseFloat( value ) ) && parseFloat( value ).toString() === value ){
							value = parseFloat( value );
						}else if( typeof value === 'string' && ( value.substr(0,1) === '{' || value.substr(0,1) === '[' ) ){
							try {
								value = JSON.parse( value );

							} catch (e) {

							}
						}else if( typeof value === 'object' && value.length && field.is('select') ){
							var new_val = {};
							for( var i = 0; i < value.length; i++ ){
								new_val[ 'n' + i ] = value[ i ];
							}

							value = new_val;
						}
					}
					lineconf[nestname] = value;
				}else{
					var newobj = lineconf;
					lineconf = {};
					lineconf[nestname] = newobj;
				}   
			}
			$.extend(true, obj, lineconf);
		};

		return obj;
	}
	conduitGeneralBaldrick = function(){
		// initialise general triggers
		$('.wp-trigger').conduitTrigger({
			method      : 'POST',
			before		: function( el ){
				var trigger = $( el ),
					app = trigger.closest('[data-app]');
				if( app.length ){
					trigger.data('data', JSON.stringify( conduitBuildData( app.data('app') ) ) );
				}else{
					if( trigger.data('data') ){
						trigger.data('data', JSON.stringify( conduitBuildData( trigger.data('data') ) ) );	
					}
				}
			}
		});
	}

	conduitPrepObject = function(){
		var obj = {};
		for( var app in conduitApp ){
			if( conduitApp[ app ].app ){
				if( conduitApp[ app ].app.is(':visible') ){
					// capture current changes
					obj[ app ] = conduitBuildData( app );
				}else{
					// changes should have been captured already
					obj[ app ] = conduitApp[ app ].data;
				}
			}
		}
		
		return obj;
	}

	conduitGetData = function( tr ){

		var id = tr.trigger.data('app'),
			data = {};

		if( conduitApp[ id ] && conduitApp[ id ].data ){
			return conduitApp[ id ].data;
		}
		return data;
	}

	conduitBuildData = function( app ){
		if( conduitApp[ app ] && conduitApp[ app ].app ){
			conduitApp[ app ].data = conduitApp[ app ].app.getObject();
		}
		return conduitApp[ app ].data;
	}	


	conduitSyncData = function( app ){
		conduitBuildData( app );
		conduitBuildUI( app );
	}

	conduitRegisterApps = function(){

		var apps = $('[data-app]').not('._bound_app');
		if( ! apps.length ){return;}
		
		apps.each( function(){
			
			var appWrapper = $( this ),
				app = appWrapper.data('app');

			conduitApp[ app ] = {
				app : appWrapper,
				data : ( uix.config[ app ] ? uix.config[ app ] : {} )
			};

			appWrapper.addClass('_bound_app');
		})

		if( uix.tabs ){
			for( var tab in uix.tabs ){
				if( uix.tabs[ tab ].default ){
					$('[data-tab="' + tab + '"]').trigger('click');
					break;
				}
			}
		}
	}

	conduitBuildUI = function( app ){
		if( conduitApp[ app ] ){
			var data = conduitApp[ app ].data;
			data._tab = {};
			for( var sub_app in conduitApp ){
				if( sub_app === app ){ continue; }
				data._tab[ sub_app ] = conduitApp[ sub_app ].data;
			}
			conduitApp[ app ].app.html( coduitTemplates[ app ]( data ) );
		}
	}

	conduitAddNode = function( node, app ){

		var id = 'nd' + Math.round(Math.random() * 99866) + Math.round(Math.random() * 99866),
			newnode = { "_id" : id },
			nodes = node.data('addNode').split('.'),
			node_default = node.data('nodeDefault'),
			node_point_record = nodes.join('.') + '.' + id,
			node_defaults = JSON.parse( '{ "_id" : "' + id + '", "_node_point" : "' + node_point_record + '" }' );

		if( node_default && typeof node_default === 'object' ){				
			$.extend( true, node_defaults, node_default );
		}			
		var node_string = '{ "' + nodes.join( '": { "') + '" : { "' + id + '" : ' + JSON.stringify( node_defaults );
		for( var cls = 0; cls <= nodes.length; cls++){
			node_string += '}';
		}
		var new_nodes = JSON.parse( node_string );
		
		conduitBuildData( app );

		$.extend( true, conduitApp[ app ].data, new_nodes );

		conduitBuildUI( app );
	};


	// trash 
	$(document).on('click', '.cf-reports-card-actions .confirm a', function(e){
		e.preventDefault();
		var parent = $(this).closest('.cf-reports-card-content');
			actions = parent.find('.row-actions');

		actions.slideToggle(300);
	});

	// bind slugs
	$(document).on('keyup change', '[data-format="slug"]', function(e){

		var input = $(this);

		if( input.data('master') && input.prop('required') && this.value.length <= 0 && e.type === "change" ){
			this.value = $(input.data('master')).val().replace(/[^a-z0-9]/gi, '_').toLowerCase();
			if( this.value.length ){
				input.trigger('change');
			}
			return;
		}

		this.value = this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();
	});
	
	// bind label update
	$(document).on('keyup change', '[data-sync]', function(){
		var input = $(this),
			syncs = $(input.data('sync'));
		
		syncs.each(function(){
			var sync = $(this);

			if( sync.is('input') ){
				sync.val( input.val() ).trigger('change');
			}else{
				sync.text(input.val());
			}
		});
	});

	// add node	
	$(document).on('click', '[data-add-node]', function(e){
		var click = $( this ),
			app = click.closest('[data-app]').data('app');
			if( app && typeof conduitApp[ app ] === 'object' ){
				e.preventDefault();
				conduitAddNode( click, app );
			}
	});
	// row remover global neeto
	$(document).on('click', '[data-remove-element]', function(e){
		var click = $(this),
			app = click.closest('[data-app]').data('app'),
			elements = $(click.data('removeElement'));
		if( click.data('confirm') ){
			if( !confirm(click.data('confirm')) ){
				return;
			}
		}
		elements.remove();
		conduitSyncData( app );
	});

	$(document).on('click', '[data-save-object]', function(e){
		e.preventDefault();
		var clicked = $( this ),
			app = $( this ).data('saveObject'),
			obj;

		if( true === app ){
			obj = conduitPrepObject();
		}else{
			obj = conduitPrepObject( app );
		}

		var data = {
			action		:	"uix_save_config",
			uix_setup	:	$('#uix_setup').val(),
			page_slug	:	uix.page_slug,
			config		:	JSON.stringify( obj ),
		};
		$.post( ajaxurl, data, function(response) {
			console.log( response );
		});

	});

	// initialize live sync rebuild
	$(document).on('change', '[data-live-sync]', function(e){
		var app = $(this).closest('[data-app]').data('app');
		conduitSyncData( app );
	});

	$(document).on( 'click', '[data-tab]', function( e ){

		e.preventDefault();
		
		var clicked = $( this ),
			tab = clicked.data('tab'),
			active = $('.active[data-tab]').data('tab')
		
		if( active ){
			conduitBuildData( active );
			if( active === tab ){
				return;
			}
			$('[data-app="' + active + '"]').empty().hide();
		}

		$('[data-tab]').removeClass('active');
		$('[data-app="' + tab + '"]').show();
		clicked.addClass('active');
		conduitBuildUI( tab );
	} );

	$('script[data-template]').each( function(){

		var element	= $(this),
			app		= element.data('template');

		coduitTemplates[ app ] = Handlebars.compile( element.html(), { data : true, trackIds : true } );
	});
	// init partials
	$('script[data-handlebars-partial]').each( function(){
		var partial = $( this );
		Handlebars.registerPartial( partial.data('handlebarsPartial'), partial.html() );
	});



	// register apps
	conduitRegisterApps();

}) );