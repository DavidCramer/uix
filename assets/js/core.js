var UIX = {};

(function() {

	jQuery( document ).ready( function( $ ) {

		$( document ).on( 'uix.init', function() {
			$( '[data-default!=""]' ).each( function() {
				var field = $( this ),
					value = field.data( 'default' );
				if ( value && value.length ) {
					field.val( value );
				}
			} );
		} );

		$( window ).load( function() {
			// main init
			$( document ).trigger( 'uix.init' );
		} );
	} );

})( window );
