/*!
This is a prototype of conduit. It still needs a full refactoring.
This is simply to get the concepts into working order. So ye, I need to work on this still.
*/
var conduitApp = {},
	coduitTemplates = {},
	conduitRegisterApps,
	conduitModal,
	conduitModalSave,
	conduitGenID,
	conduitGetData;

!( jQuery( function($){

	conduitException = function( message ){
		this.message = message;
		this.name = "ConduitException";
	}

	$.fn.conduitTrigger = function( obj ){
		var defaults = {
			method	:	'GET',
			url		:	ajaxurl
		};

		$.extend(true, defaults, obj);

		$.ajax( defaults ).success( function(){
			//console.log( arguments );
		} );


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
			if( field.prop('required') && ! field.val().length ){
				field.focus();
				throw new conduitException('requiredfield');
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

	conduitModalFooter = function( opts ){
		var buttons = opts.buttons ? opts.buttons.split(' ') : ["save"],
			points 		= opts.modal.split('.'),
			app 		= opts.app ? opts.app : points.shift(),
			data 		= { "__node_path" : points.join('.') },
			template_str = '',
			template;

		data.__app = app;

		for( var i = 0; i < buttons.length; i ++){
			template_str += $( '#__partial_' + buttons[ i ] ).length ? $( '#__partial_' + buttons[ i ] ).html() : '';
		}

		template = Handlebars.compile( template_str, { data : true } );
			
		return template( data );
	}

	conduitModal = function( opts, modal ){
		var points 		= opts.modal.split('.'),
			app 		= opts.app ? opts.app : points.shift(),
			hasDefault	= opts.default ? opts.default : null,
			template 	= Handlebars.compile( "<div data-app=\"" + opts.template + "\">{{> " + opts.template + "}}</div>", { data : true } );
			data 		= {};

		// fetch latest data object
		conduitBuildData( app );

		if( conduitApp[ app ] ){
			var tmp = conduitApp[ app ].data;
			if( hasDefault !== null ){
				data = hasDefault;
			}else{
				for( var i = 0; i < points.length; i++ ){
					if( tmp[ points[ i ] ] ){
						tmp = tmp[ points[ i ] ];
					}else{
						tmp = {};
					}
				}
				data = tmp;
			}
		}

		data.__node_path = opts.points;
		data.__app = app;

		conduitApp[ opts.template ] = {
			app		:	modal.content,
			data	:	data
		};
		coduitTemplates[ opts.template ] = template;

		return template( data );

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
			try{
				conduitApp[ app ].data = conduitApp[ app ].app.getObject();
			}catch (e){
				return false;
			}
			
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
		$(window).trigger('modal.init');
	}

	conduitSetNode = function( node, app, data ){
		var nodes = node.split('.');

		var node_string = '{ "' + nodes.join( '": { "') + '" : ' + JSON.stringify( data );
		for( var cls = 0; cls < nodes.length; cls++){
			node_string += '}';
		}
		var new_nodes = JSON.parse( node_string );
		$.extend( true, conduitApp[ app ].data, new_nodes );
		conduitBuildUI( app );		
	}
	conduitGenID = function(){
		var d = new Date().getTime();
		var id = 'ndxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = (d + Math.random()*16)%16 | 0;
			d = Math.floor(d/16);
			return (c=='x' ? r : (r&0x3|0x8)).toString(16);
		});
		return id;
	}
	conduitAddNode = function( node, app, data ){

		var id = conduitGenID(),
			newnode = { "_id" : id },
			nodes = node.data ? node.data('addNode').split('.') : node.split('.'),
			node_default = data ? data : node.data('nodeDefault'),
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
			spinner = $('.page-title-action .spinner'),
			sub_nav = $('.uix-sub-nav'),
			obj;
		
		$('.uix-notice .notice-dismiss').trigger('click');

		if( true === app ){
			obj = conduitPrepObject();
		}else{
			obj = conduitPrepObject( app );
		}
		//console.log( obj );
		clicked.addClass('saving');
		spinner.slideDown(100);
		var data = {
			action		:	uix.slug + "_save_config",
			uix_setup	:	$('#uix_setup').val(),
			page_slug	:	uix.page_slug,
			config		:	JSON.stringify( obj ),
		};
		$.post( ajaxurl, data, function(response) {
			//console.log( response );
			spinner.slideUp(100);
			clicked.removeClass('saving');
			var notice = $( coduitTemplates.__notice( response ) );
			notice.hide().insertAfter( sub_nav ).slideDown( 200 );
		});

	});

	// initialize live sync rebuild
	$(document).on('change', '[data-live-sync]', function(e){
		var app = $(this).closest('[data-app]').data('app');
		conduitSyncData( app );
	});
	$(document).on('click', 'button[data-live-sync]', function(e){
		var app = $(this).closest('[data-app]').data('app');
		conduitSyncData( app );
	});
	$(document).on('click', '.uix-notice .notice-dismiss', function(){
		var parent =  $( this ).closest( '.uix-notice' );
		parent.slideUp(200, function(){
			parent.remove();
		});
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

		coduitTemplates[ app ] = Handlebars.compile( element.html(), { data : true } );
	});
	// init partials
	$('script[data-handlebars-partial]').each( function(){
		var partial = $( this );
		Handlebars.registerPartial( partial.data('handlebarsPartial'), partial.html() );
	});
	// modal capture
	$(document).on( 'click', '[data-modal-node]', function( e ) {
		
		var clicked = $( this ),
			nodes = clicked.data('modal-node').split('.'),		
			app = clicked.data('app') ? clicked.data('app') : nodes.shift(),
			type = clicked.data('type') ? clicked.data('type') : 'save',
			data;

		if( type !== 'delete' ){
			try{
				 data = clicked.closest('.caldera-modal-wrap').getObject();
			}catch (e){
				return;
			}
		}
		if( type === 'add' ){
			conduitAddNode( nodes.join('.'), app, data );			
		}else if( type === 'delete' ){
			
			var selector = nodes.shift();
			if( nodes.length ){
				selector += '[' + nodes.join('][') + ']';
			}			
			$( '[name^="' + selector + '"]' ).remove();
			conduitBuildData( app );
			conduitBuildUI( app );
		}else{
			conduitSetNode( nodes.join('.'), app, data );
		}
		$( window ).trigger('close.modal');
	})
	$(window).on('close.modal', function( e ){
		//console.log( e );
		var active = $('.active[data-tab]').data('tab')
		if( active ){
			conduitBuildUI( active );
		}
	});


	// register apps
	conduitRegisterApps();

}) );