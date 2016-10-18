(function() {

    jQuery( document ).ready( function( $ ) {
        $(document).on('uix.init', function( e ) {
            // main init
            var selected = $('.wp-editor-area');

            selected.each(function () {
                var editor = $(this),
                    eid = editor.prop('id');

                try {
                    tinyMCE.remove();
                } catch (e) {}

                //quicktags({id : eid});
                tinyMCE.init(tinyMCEPreInit.mceInit[eid]);
                quicktags(tinyMCEPreInit.qtInit[eid]);
                QTags._buttonsInit();
                switchEditors.go(eid, 'tmce')

            })
        });
    });

})( window );