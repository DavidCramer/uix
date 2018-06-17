(function ($) {

    jQuery(document).ready(function () {

        function reset_attributes(el, name, index, id_parts, type) {
            $(el).find("[" + name + "]").each(function () {
                $(this)[type](name, function (idx, attr) {
                    var parts = attr.split('-'),
                        old_attr = parts.join('-');
                    parts[id_parts.length - 1] = index;
                    attr = parts.join('-');
                    if (name == 'id') {
                        var classnames = $('.' + old_attr);
                        classnames.removeClass(old_attr).addClass(attr.replace(/\d+/g, 0));
                    }
                    return attr;
                });
            });
        }

        function reset_repeatable_index(id) {
            var wrapper = $('[data-uix-template="' + id + '"'),
                id_parts = id.split('-');

            wrapper.children().each(function (index, el) {
                id_parts[id_parts.length - 1] = index;
                var new_id = id_parts.join('-');
                reset_attributes(el, 'name', index, id_parts, 'attr');
                reset_attributes(el, 'data-uix-template', index, id_parts, 'attr');
                reset_attributes(el, 'id', index, id_parts, 'prop');
                reset_attributes(el, 'for', index, id_parts, 'attr');
                reset_attributes(el, 'data-target', index, id_parts, 'attr');
                //reset_attributes(el, 'data-for', index, id_parts, 'attr');
            })
        }

        $(document).on('click', '[data-uix-repeat]', function (e) {
            var clicked = $(this),
                id = clicked.data('uixRepeat'),
                template = '';
            template = $('#' + id + '-tmpl').html();
            template = $(template.replace(/{{_inst_}}/g, 0)).hide();
            clicked.parent().prev().append(template);
            template.slideDown(100);
            //reset_repeatable_index( id );

            $(document).trigger('uix.init');
	        setTimeout( function(){
		        $(document).trigger('uix.save');
	        }, 200 );
        });

        $(document).on('click', '.uix-remover', function (e) {
            var clicked = $(this),
                template = clicked.closest('[data-uix-template]'),
                id = template.data('uixTemplate');
            $(this).parent().slideUp(100, function () {
                $(this).remove();
	            $(document).trigger('uix.init');
	            setTimeout( function(){
		            $(document).trigger('uix.save');
	            }, 200 );
            });
            //reset_repeatable_index( id );

        })

        $(document).on('uix.init', function () {
            var wrappers = $('[data-uix-template]');
            wrappers.each(function () {
                var id = $(this).attr('data-uix-template');
                reset_repeatable_index(id);

            })
        });

        $('[data-uix-repeat]').each(function () {
            var id = $(this).attr('data-uix-repeat'),
                elesclass = $('.' + id);

            elesclass.removeClass(id);

            id = id.replace(/\d+/g, 0);
            elesclass.addClass(id);
            $(this).attr('data-uix-repeat', id);

        });

        $(document).trigger('uix.init');

    })


})(jQuery);
