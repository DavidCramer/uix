
var uix_edit_state = false;

(function($){
    jQuery( document ).ready( function(){
        var spinner,
            trigger;
        $('.uix-ajax').baldrick({
            'request': window.location.href,
            before : function( el, ev ){
                if( spinner ){
                    spinner.remove();
                }
                spinner = $( '<span class="uix-ajax spinner"></span>' );
                if( ev.originalEvent && ev.originalEvent.explicitOriginalTarget ){
                    //$( ev.originalEvent.explicitOriginalTarget ).prop('disabled', 'disabled' ).addClass('disabled');
                    spinner.addClass('inline');
                }
                $(el).find('.uix-title').append( spinner );
            },
            callback : function( obj, ev ){

                if( ev && ev.originalEvent && ev.originalEvent.explicitOriginalTarget ) {
                    spinner.removeClass( 'spinner' ).addClass('dashicons dashicons-yes');
                    setTimeout( function(){
                        spinner.fadeOut( 1000, function(){
                            spinner.remove();
                        });
                    }, 1000 );

                    $(ev.originalEvent.explicitOriginalTarget).prop('disabled', false).removeClass('disabled');
                }else{
                    spinner.remove();
                    obj.params.trigger.find('.ajax-triggered').removeClass('ajax-triggered');
                }
                uix_edit_state = false;
            }
        });

        $('form.uix-ajax').each( function(){
            var form = $( this );
            if( !form.find('button[type="submit"]').length ){
                form.on('change', '[name]', function( e ){
                    $(this).addClass('ajax-triggered');
                    form.trigger( 'submit' );
                })
            }else{
                form.on( 'change', '[name]', function(){
                    uix_edit_state = true;
                });
            }
            $( document ).on('uix.init', function(){
                form.trigger( 'submit' );
            })
        });
    })

    // check for a button




    window.onbeforeunload = function(e) {

        if( false === uix_edit_state ){ return; }

        var dialogText = 'confirm';
        e.returnValue = dialogText;
        return dialogText;
    };

})(jQuery);
