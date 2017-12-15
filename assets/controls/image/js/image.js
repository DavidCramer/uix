(function () {

    jQuery(document).ready(function ($) {

        // Uploading files
        var file_frame;
        var field;
        var preview_size;
        jQuery(document).on('click', '.uix-image-control-button', function (event) {

            event.preventDefault();

            field = $(this).data('target');
            preview_size = $(this).data('size');

            // If the media frame already exists, reopen it.
            if (file_frame) {
                // Open frame
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select a image to upload',
                button: {
                    text: 'Use this image',
                },
                multiple: false	// Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                var url = attachment.url;
                if (attachment.sizes[preview_size]) {
                    url = attachment.sizes[preview_size].url;
                }
                // Do something with attachment.id and/or attachment.url here
                $('#' + field + '-wrap').html('<img src="' + url + '" class="uix-image-control-preview"><a href="#" class="uix-image-control-remove" data-target="' + field + '"><span class="dashicons dashicons-no"></span></a>');
                $('#' + field + '-control').val(attachment.id);

            });

            // Finally, open the modal
            file_frame.open();
        });
        jQuery(document).on('click', '.uix-image-control-remove', function (event) {
            event.preventDefault();
            var field = $(this).data('target');
            $('#' + field + '-wrap').html('');
            $('#' + field + '-control').val('');
        });

    });

})(window);