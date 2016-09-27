var uix_related_post_handler,
    uix_related_post_before;
( function( $ ){
    jQuery( function( $ ){


        uix_related_post_before = function( el, ev ){
            var search = $( el ),
                items = [],
                page = 1,
                wrap = search.closest('.uix-control-input').find('.uix-post-relation');

            if( ev.type === 'paginate' ){
                page = search.data('paginate');
            }
            wrap.find('.uix-post-relation-id' ).each( function(){
                items.push( this.value );
            });

            search.data({ selected : items, page : page });
        }

        uix_related_post_handler = function( obj ){
            var wrapper = obj.params.trigger.parent().find('.uix-post-relation-results');
            wrapper.html( obj.data.html );
        };


        $( document ).on('click', '.uix-add-relation', function( e ) {
            var clicked = $(this),
                panel = clicked.closest('.uix-control-input').find('.uix-post-relation-panel'),
                input = panel.find('.uix-ajax');

            panel.toggle();
            if( panel.is(':visible') ) {
                input.val('').trigger('input').focus();
            }else{
                input.parent().find('.uix-post-relation-results').html('');
            }


        });
        $( document ).on('click', '.uix-post-relation-page', function( e ){
            var clicked = $( this ),
                search = clicked.closest('.uix-post-relation-panel').find('.uix-ajax');

            search.data('paginate', clicked.data('page') ).trigger('paginate');

        });

        $( document ).on('click', '.uix-post-relation-add', function(){

            var clicked = $( this ),
                oitem = clicked.parent(),
                wrap = clicked.closest('.uix-control-input').find('.uix-post-relation'),
                limit = parseFloat( wrap.data('limit') ),
                items,
                panel = wrap.parent().find('.uix-post-relation-footer, .uix-post-relation-panel'),
                item;


            clicked.removeClass('uix-post-relation-add dashicons-plus').addClass('uix-post-relation-remover dashicons-no-alt');
            item = oitem.clone();
            item.appendTo( wrap ).hide();
            item.find('.uix-post-relation-id').prop( 'disabled', false );
            item.show();
            oitem.remove();


            if( wrap.parent().find( '.uix-post-relation-results > .uix-post-relation-item' ).length <= 0 ){
                wrap.parent().find( '.uix-ajax' ).trigger('input');
            }

            items = wrap.children().length;

            if( items >= limit && limit > 0 ){
                panel.hide();
            }else{
                panel.show();
            }

        });

        $( document ).on('click', '.uix-post-relation-remover', function(){

            var clicked = $( this ),
                item = clicked.parent(),
                wrap = clicked.closest('.uix-control-input').find('.uix-post-relation'),
                limit = parseFloat( wrap.data('limit') ),
                items,
                panel = wrap.parent().find('.uix-post-relation-footer, .uix-post-relation-panel');

            item.remove();

            items = wrap.children().length;

            if( items >= limit && limit > 0 ){
                panel.hide();
            }else{
                if( !panel.is(':visible') ){
                    panel.show();
                    panel.find('.uix-ajax').val('').trigger('input').focus();
                }

            }
        });

    });
})( jQuery )