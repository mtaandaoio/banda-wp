(function(jQuery) {
    jQuery( document ).ready(function() {

        if (typeof fesiWooCart === "undefined") {
            var fesiWooCart = fesiWooCartAdditional
        }
        var lastUsedShowProducts;

        var productsListSelector = '.festi-cart-products';

        var festiSetTimeout;

        //Icon Actions on Hover

        jQuery('body').on('mouseenter touchend', 'a.festi-cart', function() {
            showOnHoverIcon(this);
        });

        function showOnHoverIcon(selector)
        {
            if (jQuery(selector).find('.festi-cart-icon').length == 0
                || jQuery(selector).find('.festi-cart-icon.festi-on-hover').length == 0) {
                return true;
            }

            jQuery(selector).find('.festi-cart-icon').hide();
            jQuery(selector).find('.festi-cart-icon.festi-on-hover').show();
        }

        function hideOnHoverIcon(selector) {
            if (jQuery(selector).find('.festi-cart-icon').length == 0) {
                return true;
            }

            if (jQuery(productsListSelector).css('display') == 'block') {
                return true;
            }

            jQuery(selector).find('.festi-cart-icon').show();
            jQuery(selector).find('.festi-cart-icon.festi-on-hover').hide();
        }


        function getPositionProductList(element) {
            var windowWidth = jQuery(window).width();
            var offset = element.offset();

            var height = element.outerHeight();

            var width = element.outerWidth();

            var selectorWidth = jQuery(productsListSelector).width();

            if (typeof fesiWooCart.productListAligment != "undefined") {
                if (fesiWooCart.productListAligment == 'left') {
                    if ((offset.left + selectorWidth) > windowWidth) {
                        offset.left = offset.left - selectorWidth + width;
                    }
                } else {
                    if ((offset.left - selectorWidth) > 0) {
                        offset.left = offset.left - selectorWidth + width;
                    }
                }
            }

            offset.top = offset.top + height - 1;
            return offset;
        }

        jQuery('body').on('hover', '.festi-cart-products', function () {
            festiCartProductsMouseRemove = 0;
        });

        jQuery('body').on('click', '.festi-cart.festi-cart-click', function () {
            festiCartClick(this);

            return false;
        });


        jQuery('body').on('click', function (event) {
            if (jQuery(event.target).closest(productsListSelector).length == 0) {
                jQuery(productsListSelector).hide();
                jQuery('.festi-cart-arrow').hide();
                jQuery("a.festi-cart").removeClass("festi-cart-active");
                hideOnHoverIcon('.festi-cart.festi-cart-click');
            }
        });

        function festiCartClick(element) {
            if (jQuery(productsListSelector).css('display') != 'none' && jQuery(element).get(0) == lastUsedShowProducts.get(0)) {
                jQuery(productsListSelector).hide();
                jQuery('.festi-cart-arrow').hide();
                jQuery(element).removeClass("festi-cart-active");
            } else {
                jQuery(productsListSelector).show();
                lastUsedShowProducts = jQuery(element);

                offset = getPositionProductList(jQuery(element));
                jQuery(productsListSelector).offset({top: offset.top, left: offset.left});

                elementOffset = jQuery(element).offset();
                jQuery('.festi-cart-arrow').show();
                jQuery('.festi-cart-arrow').offset({
                    top: offset.top,
                    left: elementOffset.left + (jQuery(element).width() / 2)
                });
                jQuery(element).addClass("festi-cart-active");
            }

            if (jQuery("#festi-cart-products-list-body").length > 0) {
                jQuery("#festi-cart-products-list-body").scrollTop(0);
            }
        }

        function festiCartMouseOver(element) {
            festiCartProductsMouseRemove = 0;

            jQuery(productsListSelector).show();
            lastUsedShowProducts = jQuery(element);

            offset = getPositionProductList(jQuery(element));
            jQuery(productsListSelector).offset({top: offset.top, left: offset.left});
            elementOffset = jQuery(element).offset();
            jQuery('.festi-cart-arrow').show();
            jQuery('.festi-cart-arrow').offset({
                top: offset.top,
                left: elementOffset.left + (jQuery(element).width() / 2)
            });
            jQuery(element).addClass("festi-cart-active");

            if (jQuery("#festi-cart-products-list-body").length > 0) {
                jQuery("#festi-cart-products-list-body").scrollTop(0);
            }
        }


        jQuery('body').on('mouseover', '.festi-cart.festi-cart-hover', function () {

            festiCartMouseOver(this);

            return false;
        });

        jQuery('body').on('mouseover', function (event) {
            if (jQuery(event.target).closest(productsListSelector).length == 0 && jQuery(".festi-cart.festi-cart-hover").length != 0) {
                clearTimeout(festiSetTimeout);
                festiCartProductsMouseRemove = 1;
                festiSetTimeout = setTimeout(function () {
                    hideProductsList();
                }, 100);
            }
        });

        function hideProductsList() {
            if (festiCartProductsMouseRemove == 1) {
                jQuery(productsListSelector).hide();
                jQuery('.festi-cart-arrow').hide();
                jQuery("a.festi-cart").removeClass("festi-cart-active");
                hideOnHoverIcon('.festi-cart.festi-cart-hover');
            }
        }

        function getCartProductData(id) {
            if (!id || typeof(festiCartProductsItems) === 'undefined') {
                return false;
            }

            if (typeof(festiCartProductsItems[id]) === 'undefined') {
                return false;
            }

            return festiCartProductsItems[id];
        }

        function doRemoveProductFromCart(element, itemHash = null) {
            var productID = jQuery(element).attr('data-id');
            var productData = getCartProductData(productID);
            if (productData) {
                jQuery(document).trigger(
                    "remove_product_from_cart", [element, productData]
                );
            }
            if (!itemHash) {
                var itemHref = jQuery(element).attr('href');
            } else {
                var itemHref = jQuery(element).attr('name');
            }
            var productKey = getParameterByName(itemHref, 'remove_item');
            if (!productKey) {
                selector = jQuery(element).parent().parent();
                productKey = itemHref
            }

            showBlockUi("table.festi-cart-list");
            showBlockUi(".cart_list.product_list_widget ");

            var data = {
                action: 'remove_product',
                deleteItem: productKey
            };

            var instance = element;

            jQuery.post(fesiWooCart.ajaxurl, data, function (productCount) {

                var productCount = productCount;

                var data = {
                    action: 'banda_get_refreshed_fragments'
                };

                jQuery.post(fesiWooCart.ajaxurl, data, function (response) {
                    fragments = response.fragments;

                    if (fragments) {
                        jQuery.each(fragments, function (key, value) {
                            jQuery(key).replaceWith(value);
                        });

                        if (!jQuery(instance).hasClass("fecti-cart-from-widget")) {
                            jQuery('a.festi-cart').addClass("festi-cart-active");
                        }
                    }

                    if (productCount < 1) {
                        var parent = jQuery(lastUsedShowProducts).parent()
                        if (parent.hasClass("widget")) {
                            jQuery(productsListSelector).fadeOut();
                        }
                    }

                })
            });
        }

        jQuery('body').on('click', '.festi-cart-remove-product, .cart_list.product_list_widget .remove', function () {
            doRemoveProductFromCart(this);

            return false;
        });

        function getParameterByName(url, name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(url);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }

        function refreshCartAfterAddToCart() {
            var data = {
                action: 'banda_get_refreshed_fragments'
            };

            jQuery.post(fesiWooCart.ajaxurl, data, function (response) {
                fragments = response.fragments;

                if (fragments) {
                    jQuery.each(fragments, function (key, value) {
                        jQuery(key).replaceWith(value);
                    });
                }

                jQuery("body").trigger("onShowPupUpCart");
            })
        } // end refreshCart

        jQuery(window).scroll(function () {
            if (jQuery(productsListSelector).css('display') != 'none' && jQuery(productsListSelector).length != 0) {
                var offset = getPositionProductList(lastUsedShowProducts);
                if ((offset.top - jQuery(document).scrollTop()) > 0) {
                    jQuery(productsListSelector).offset({top: offset.top, left: offset.left});
                    elementOffset = jQuery(lastUsedShowProducts).offset();
                    jQuery('.festi-cart-arrow').offset({
                        top: offset.top,
                        left: elementOffset.left + (jQuery(lastUsedShowProducts).width() / 2)
                    });
                } else {
                    jQuery(productsListSelector).hide();
                    jQuery('.festi-cart-arrow').hide();
                    jQuery("a.festi-cart").removeClass("festi-cart-active");
                }

            }
        });


        if (jQuery('.festi-cart-horizontal-position-center').length > 0) {
            var documentWidth = jQuery(document).width();
            var windowCartOuterWidth = jQuery('.festi-cart-horizontal-position-center').outerWidth()

            var leftPosition = (documentWidth - windowCartOuterWidth) / 2;

            jQuery('.festi-cart-horizontal-position-center').css({
                left: leftPosition,
            });

            jQuery('.festi-cart-horizontal-position-center').show();
        }

        if (jQuery('.festi-cart-vertical-position-middle').length > 0) {
            var documentHeight = jQuery(document).height();
            var windowCartOuterHeight = jQuery('.festi-cart-vertical-position-middle').outerHeight()

            var topPosition = (documentHeight - windowCartOuterHeight) / 2;

            jQuery('.festi-cart-vertical-position-middle').css({
                top: topPosition,
            });

            jQuery('.festi-cart-vertical-position-middle').show();
        }

        jQuery('body').on('added_to_cart', function () {
            refreshCartAfterAddToCart();
        });

        jQuery("body").on("onShowPupUpCart", function () {
            jQuery('#festi-cart-pop-up-content').bPopup({
                modalClose: true,
                positionStyle: 'absolute'
            });
        });


        if (fesiWooCart.isMobile && fesiWooCart.isEnabledPopUp) {
            jQuery('body').on('click', '.festi-cart', function () {
                jQuery('.festi-cart-pop-up-header').css('display', 'none');
                jQuery('#festi-cart-pop-up-content').bPopup({
                    modalClose: true,
                    positionStyle: 'absolute'
                });
                return false;
            });
        }


        jQuery("body").on("onShowPupUpCart", function () {
            jQuery('.festi-cart-pop-up-header').css('display', 'block');
            jQuery('#festi-cart-pop-up-content').bPopup({
                modalClose: true,
                positionStyle: 'absolute'
            });
        });

        function showBlockUi(element) {
            jQuery(element).fadeTo("400", "0.4").block(
                {
                    message: null,
                    overlayCSS: {
                        background: "transparent url('" + fesiWooCart.imagesUrl + "ajax-loader.gif') no-repeat center",
                        opacity: .6
                    }
                }
            );
        } // end showBlockUi

        function hideBlockUi(element) {
            jQuery(element).unblock().fadeTo("400", "1");
        } // end hideBlockUi


        jQuery('body').on('change keyup', 'div.itemQuantity input[type=number]', function () {
            var value = this.value;
            var currentQuantity = parseFloat(value);
            var itemKey = jQuery(this).attr('name');
            var minPossibleValue = 1;

            if (value < minPossibleValue) {
                doRemoveProductFromCart(this, itemKey)
            } else {
                jQuery.ajax({
                    type: 'POST',
                    url: fesiWooCart.ajaxurl,
                    dataType: "json",
                    data: {
                        action: 'update_total_price',
                        quantity: currentQuantity,
                        itemKey: itemKey
                    },
                    success: function (response) {
                        if (response.error) {
                            jQuery('.festi-cart-error-message').text(
                                'Some error has occurred, please reload page or try it later'
                            ).show();
                            console.error(response.error.message);
                        } else {
                            jQuery('.festi-cart-total').html(response.totalPrice);
                            jQuery('.festi-cart-quantity').text(response.totalItems);
                            jQuery('.budgeCounter').text(response.totalItems);
                            jQuery('.subtotal').prepend('Subtotal: : ');
                        }
                    },
                    error: function (response) {
                        jQuery('.festi-cart-error-message').text(
                            'Some error has occurred, please reload page or try it later'
                        ).show();
                        console.error('There was a failure ajax request');
                    }
                });
            }
        });
    });
}(jQuery));