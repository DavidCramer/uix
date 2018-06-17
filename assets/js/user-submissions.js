(function() {

	jQuery( document ).ready( function( $ ) {

		// core user_submissions object
		console.log( USRSUB );

		// USRSUB localized data

		var template = Handlebars.compile( $('#user-sumissions-template').html() );
		// define any global vars within this scope

		$('#user-submissions-render').html( template( USRSUB ) );

		$(document).on('click', '.user-submissions-form', function(){
			var clicked = $(this),
				wrapper = $('.' + clicked.data('id') );

			$('.user-submissions-entry-wrapper').slideUp();
			if( clicked.hasClass('open') ){
				clicked.removeClass('open');
			} else {
				$( '.user-submissions-form.open' ).removeClass( 'open' );
				clicked.addClass( 'open' );
				wrapper.slideDown();
			}


		});


	} );

})( window );
