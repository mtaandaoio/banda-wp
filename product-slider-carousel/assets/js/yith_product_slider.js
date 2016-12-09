jQuery(document).ready(function ($) {

    var products_sliders = $('.ywcps-wrapper');

    /*************************
     * PRODUCTS SLIDER
     *************************/


    if ($.fn.owlCarousel  && products_sliders.length) {
        var product_slider = function (t) {


                var cols = parseInt(t.data('n_items')),
                    autoplay = parseInt(t.data('auto_play')) > 0,
                    responsive = t.data('en_responsive'),
                    n_item_desk_small = parseInt(t.data('n_item_desk_small')),
                    n_item_tabl = parseInt(t.data('n_item_tablet')),
                    n_item_mob = parseInt(t.data('n_item_mobile')),
                    is_loop = t.data('is_loop'),
                    pag_speed = parseInt(t.data('pag_speed')),
                    stop_hov = t.data('stop_hov'),
                    show_nav = t.data('show_nav'),
                    en_rtl = t.data('en_rtl'),
                    anim_in = t.data('anim_in'),
                    anim_out = t.data('anim_out'),
                    anim_speed = parseInt(t.data('anim_speed')),
                    show_dot_nav = t.data('show_dot_nav');


                if (!responsive) {
                    n_item_mob = n_item_tabl = cols;
                }
                var owl = t.find('.ywcps-products'),
                    block_params = {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        },
                        ignoreIfBlocked: true
                    };

                owl.on('initialize.owl.carousel', function(e){
                    var slider_container = e.currentTarget;

                    $(slider_container).parents('.ywcps-wrapper').block(block_params);

                });
                owl.on('initialized.owl.carousel ', function (e) {

                    var slider_container = e.currentTarget;
                    $(slider_container).parents('.ywcps-wrapper').unblock();
                    $(slider_container).parents('.ywcps-slider').css({'visibility':'visible'});

                });

                owl.owlCarousel({
                    responsiveClass: responsive,
                    animateOut: anim_out,
                    animateIn: anim_in,

                    responsive: {
                        0: {
                            items: n_item_mob
                        },
                        479: {
                            items: n_item_tabl
                        },
                        767: {
                            items: n_item_desk_small
                        },
                        1100: {
                            items: cols,
                            slideBy: cols,
                        }
                    },
                    items: cols,
                    autoplay: autoplay,
                    autoplayTimeout: parseInt(t.data('auto_play')),
                    autoplayHoverPause: stop_hov,
                    loop: is_loop,
                    rtl: en_rtl,
                    navSpeed: pag_speed,
                    dots: show_dot_nav,
                    nav: false,
                    addClassActive: true
                });


                var el_prev = t.find('.ywcps-nav-prev'),
                    el_next = t.find('.ywcps-nav-next'),
                    id_prev = el_prev.attr('id'),
                    id_next = el_next.attr('id');

                if (!show_nav) {
                    $('#' + id_prev).hide();
                    $('#' + id_next).hide();
                }

                if (!show_dot_nav)
                    $('.owl-theme .owl-controls').hide();

                // Custom Navigation Events
                t.on('click', '#' + id_next, function () {
                    owl.trigger('next.owl.carousel');
                });

                t.on('click', '#' + id_prev, function () {
                    owl.trigger('prev.owl.carousel');
                });

                if (products_sliders.parent().hasClass('banda')) {
                    t.on('translated.owl.carousel', function () {

                        if (typeof apply_hover == 'function')
                            apply_hover();
                        if (typeof yit_change_thumb_loop == 'function')
                            yit_change_thumb_loop();
                    });
                }
        };


        // initialize slider in only visible tabs
        products_sliders.each(function(){
            var t = $(this);
            if( ! t.closest('.panel.group').length || t.closest('.panel.group').hasClass('showing')  ){
                product_slider( t );
            }
        });

        $('.tabs-container').on( 'tab-opened', function( e, tab ) {
            product_slider( tab.find( products_sliders ) );
        });



    }

});
