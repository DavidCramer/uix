(function() {

    jQuery( function( $ ){
        $( document ).on( 'change', '.uix-control .switch', function( e ){
            var clicked     = $( this ),
                control     = $( '#' + clicked.data('for') );

            if( control.is(':checked') ){
                clicked.addClass( 'active' );                          
            }else{
                clicked.removeClass( 'active' );
            }

        } );

        $( '.uix-control .switch' ).trigger( 'change' );
    });

})( jQuery );