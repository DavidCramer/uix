var conduitApp = {},
	conduitRegisterApps,
	conduitGetData;
	

!( jQuery( function($){

	conduitGeneralBaldrick = function(){
		// initialise general baldrick triggers
		$('.wp-baldrick').baldrick({
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
			conduitApp[ app ].data = conduitApp[ app ].app.formJSON();
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
				id = appWrapper.data('app'),
				data = appWrapper.formJSON();

			conduitApp[ id ] = {
				app : appWrapper,
				data : ( data.init ? data.init : data )
			};

			appWrapper.addClass('_bound_app').addClass( 'app-' + id ).data({
				'template'	:	'#' + id + '-template',
				'request'	:	'conduitGetData'
			});

			$('.app-' + id ).baldrick({
				method      : 'POST',
				target		: this,
				event		: 'refresh',
				callback	: function(){
					conduitRegisterApps();
					conduitGeneralBaldrick();
				}
			}).trigger('refresh');

		})

	}

	conduitBuildUI = function( app ){
		$('.app-' + app ).trigger('refresh');
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

	// initialize live sync rebuild
	$(document).on('change', '[data-live-sync]', function(e){
		var app = $(this).closest('[data-app]').data('app');
		conduitSyncData( app );
	});

	// init partials
	$('script[data-handlebars-partial]').each( function(){
		var partial = $( this );
		Handlebars.registerPartial( partial.data('handlebarsPartial'), partial.html() );
	});

	// register apps
	conduitRegisterApps();

}) );