(function( $ ) {
    $(document).ready(function () {
        $('.jSDate').each(function () {
            var _this = $(this),
                params = _this.data();

            $(this).datepicker(params);
        });
    });
})( jQuery );