var UIX = {};

(function() {


	UIX = function(){

		var attributes = {}

		// UI templates
		var create = function( tag, args ){

			var element = document.createElement( tag );

			for( var attribute in attributes ){
				element.setAttribute( attribute, attributes[ attribute ] );				
			}

			return element;
		}
		var uiBox = function(){

			return create( 'div' );

		}

		var self = {
			set	: {
				atts	: function( newAtts ){
					for( var att in newAtts ){
						attributes[ att ] = newAtts[ att ];
					}
				}
			},
			render : uiBox

		}

		return self;
	}

	window.addEventListener('click', function (e) {
		console.log( e.target.classList.contains( 'ui-add-box' ) );
	});
	/*$( document ).on( 'click', '.ui-add-box', function( e ){
		
		var trigger		=	$( this ),
			wrapper		=	trigger.closest('.widget-content'),
			UI			=	UIX();

		// create a new box group
		UI.set.atts({
			"class" : 'fs-group'
		});
		
		wrapper.append( UI.render() )

	} )*/
	


})( window );