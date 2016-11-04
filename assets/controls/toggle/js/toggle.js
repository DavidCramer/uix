(function() {

    jQuery( function( $ ){
        $( document ).on( 'change', '.toggle-checkbox', function( e ){
            var clicked     = $( this ),
                parent      = clicked.closest( '.uix-section-content' ),                
                toggleAll   = parent.find( '[data-toggle-all="true"]' ),
                allcount    = parent.find( '.uix-control .switch > input' ).not( toggleAll ).length,
                tottlecount = parent.find( '.uix-control .switch > input:checked' ).not( toggleAll ).length;

            if( clicked.data('value') ){
                clicked.prop( 'checked', true );
                console.log( clicked.is(':checked') + ' - ' + clicked.prop('name')  );
                clicked.data('value', false );
            }
            if( clicked.is(':checked') ){
                clicked.parent().addClass( 'active' );
                if( allcount === tottlecount ){
                   toggleAll.prop( 'checked', true ).parent().addClass( 'active' );
                }

            }else{
                clicked.parent().removeClass( 'active' );
                if( toggleAll.length ){
                    toggleAll.prop( 'checked', false ).parent().removeClass( 'active' );
                }
            }

        } );
        $( document ).on( 'uix.init', function() {
            $('.uix-control .toggle-checkbox').trigger('change');
        });
        $( document ).on('change', '[data-toggle-all="true"]', function(e){
            var clicked = $( this ),
                parent = clicked.closest( '.uix-section-content' );

            if( !clicked.data('init') ) {
                // ignore the first init as this will disable all in the group.
                clicked.data('init', true );
            } else {
                parent.find('.uix-control .switch > input').not(this).prop('checked', this.checked).trigger('change');
            }
        });

    });



})( jQuery );