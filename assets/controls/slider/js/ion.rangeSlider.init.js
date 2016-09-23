( function( $ ){
    jQuery( function( $ ){
        $( document ).on('uix.init', function(){
            $(".uix-slider").ionRangeSlider();
        });
    });
    $( document ).trigger('uix.init');
})( jQuery )