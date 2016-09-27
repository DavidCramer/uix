var UIX = {};

(function() {

    jQuery( document ).ready( function( $ ){
        jQuery( window ).load( function() {
            // main init
            $(document).trigger('uix.init');
        });
    });

})( window );