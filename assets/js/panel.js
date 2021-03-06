(function( $ ) {

    $( document ).on( 'click', '.uix-tab-trigger', function( e ){
        e.preventDefault();
        var clicked  = $( this ),
            target   = $( clicked.attr('href') ),
            wrapper  = clicked.closest('.uix-panel-inside'),
            tabs     = wrapper.find('> .uix-panel-tabs').children(),
            sections = wrapper.find('> .uix-sections').children();

        tabs.attr('aria-selected', false );
        clicked.parent().attr('aria-selected', true );

        sections.attr('aria-hidden', true );
        target.attr('aria-hidden', false );

    });

})( jQuery );