import Vue from 'vue';

//let UIX = UIX ? UIX : {};

new Vue({
	el: 'form[data-uix]',
	data: UIX
});


(function() {

    jQuery( document ).ready( function( $ ){

       $( document ).on('uix.init', function() {
           $('[data-default]').each(function () {
                var field = $(this);
                field.val(field.data('default'));
            });
        });

       $( window ).load( function() {
            // main init
            $(document).trigger('uix.init');
        });
    });


})( window );