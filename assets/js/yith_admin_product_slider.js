/*Select2 for ajax category search*/
var  ywcps_resize_thickbox = function( w, h ) {

    w   =   w || 400;
    h   =   h || 350;


    var myWidth = w,
        myHeight = h;


    var tbWindow = jQuery('#TB_window'),
        tbFrame = jQuery('#TB_iframeContent'),
        wpadminbar = jQuery('#wpadminbar'),
        width = jQuery(window).width(),
        height = jQuery(window).height(),

        adminbar_height = 0;

    if (wpadminbar.length) {
        adminbar_height = parseInt(wpadminbar.css('height'), 10);
    }

    var TB_newWidth = ( width < (myWidth + 50)) ? ( width - 50) : myWidth;
    var TB_newHeight = ( height < (myHeight + 45 + adminbar_height)) ? ( height - 45 - adminbar_height) : myHeight;

    tbWindow.css({
        'marginLeft': -(TB_newWidth / 2),
        'marginTop' : -(TB_newHeight / 2),
        'top'       : '50%',
        'width'     : TB_newWidth,
        'height'    : TB_newHeight
    });

    tbFrame.css({
        'padding': '10px',
        'width'  : TB_newWidth - 20,
        'height' : TB_newHeight - 50
    });
}
jQuery( function ( $ ) {


    function getEnhancedSelectFormatString() {
        var formatString = {
            formatMatches: function( matches ) {
                if ( 1 === matches ) {
                    return ywcps_admin_i18n.i18n_matches_1;
                }

                return ywcps_admin_i18n.i18n_matches_n.replace( '%qty%', matches );
            },
            formatNoMatches: function() {
                return ywcps_admin_i18n.i18n_no_matches;
            },
            formatAjaxError: function( jqXHR, textStatus, errorThrown ) {

                return ywcps_admin_i18n.i18n_ajax_error;
            },
            formatInputTooShort: function( input, min ) {
                var number = min - input.length;

                if ( 1 === number ) {
                    return ywcps_admin_i18n.i18n_input_too_short_1;
                }

                return ywcps_admin_i18n.i18n_input_too_short_n.replace( '%qty%', number );
            },
            formatInputTooLong: function( input, max ) {
                var number = input.length - max;

                if ( 1 === number ) {
                    return ywcps_admin_i18n.i18n_input_too_long_1;
                }

                return ywcps_admin_i18n.i18n_input_too_long_n.replace( '%qty%', number );
            },
            formatSelectionTooBig: function( limit ) {
                if ( 1 === limit ) {
                    return ywcps_admin_i18n.i18n_selection_too_long_1;
                }

                return ywcps_admin_i18n.i18n_selection_too_long_n.replace( '%qty%', limit );
            },
            formatLoadMore: function( pageNumber ) {
                return ywcps_admin_i18n.i18n_load_more;
            },
            formatSearching: function() {
                return ywcps_admin_i18n.i18n_searching;
            }
        };

        return formatString;
    }



    $( 'body' ).on( 'ywcps-enhanced-select-init', function() {

            $( ':input.ywcps_enhanced_select' ).filter( ':not(.enhanced)' ).each( function() {
                var select2_args = {
                    allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
                    placeholder: $( this ).data( 'placeholder' ),
                    minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                    escapeMarkup: function( m ) {
                        return m;
                    },
                    ajax: {
                        method: 'GET',
                        url:         ywcps_admin_i18n.ajax_url,
                        dataType:    'json',
                        quietMillis: 250,
                        data: function( term, page ) {
                            return {
                                term:     term,
                                action:   $( this ).data( 'action' ) || 'yith_json_search_product_categories',
                                security: ywcps_admin_i18n.search_categories_nonce,
                                plugin: ywcps_admin_i18n.plugin_nonce
                            };
                        },
                        results: function( data, page ) {

                            var terms = [];
                            if ( data ) {
                                $.each( data, function( id, text ) {
                                    terms.push( { id: id, text: text } );
                                });
                            }
                            return { results: terms };
                        },
                        cache: true
                    }
                };


                if ( $( this ).data( 'multiple' ) === true ) {
                    select2_args.multiple = true;
                    select2_args.initSelection = function( element, callback ) {
                        var data     = $.parseJSON( element.attr( 'data-selected' ) );
                        var selected = [];

                        $( element.val().split( "," ) ).each( function( i, val ) {
                            selected.push( { id: val, text: data[ val ] } );
                        });
                        return callback( selected );
                    };
                    select2_args.formatSelection = function( data ) {
                        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                    };
                } else {
                    select2_args.multiple = false;
                    select2_args.initSelection = function( element, callback ) {

                        var data = {id: element.val(), text: element.attr( 'data-selected' )};
                        return callback( data );
                    };
                }

                select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

                $( this ).select2( select2_args ).addClass( 'enhanced' );
            });


        }).trigger( 'ywcps-enhanced-select-init' );

});
