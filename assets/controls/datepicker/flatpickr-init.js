(function ($) {

    // Add Color Picker to all inputs that have 'color-field' class
    $(function () {
        $(document).on('uix.init', function () {

            $('.flatpickr').each(function () {
                if (!this._flatpickr) {
                    this.flatpickr($(this).data());
                }
            });

        });
    });

})(jQuery);