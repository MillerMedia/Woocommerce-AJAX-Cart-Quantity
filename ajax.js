(function ($) {
    $(document).ready(function () {
        jQuery.ajax({
            url: cart_page.wpajaxurl,
            data: ({action: 'get_ajax_cart_qty'}),
            success: function (data) {
                var qty = data.slice(0, -1);
                $('a[href$="' + cart_page.cart_url + '"]').each(function(){
                   $(this).append(' (' + qty.toString() + ') ').css('white-space', 'nowrap');
                });
            }
        });
    });
})(jQuery);