(function(jQuery) {
    jQuery( document ).ready(function() {
        if (jQuery('a#BandaWooCartProInMenu').length > 0) {
            jQuery('a#BandaWooCartProInMenu').parent().parent().replaceWith(fesiWooCartInMenu.cartContent);
        }
    })
}(jQuery));