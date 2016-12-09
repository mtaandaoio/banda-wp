<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}
$animations =   ywcps_animations_list();

$args   =   array(
    'label'    => __( 'Product Slider', 'yith-banda-product-slider-carousel' ),
    'pages'    => 'yith_wcps_type', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'tabs'     => array(
        'settings' => array(
            'label'  => __( 'Settings', 'yith-banda-product-slider-carousel' ),
            'fields' => array(

                    'ywcps_categories' => array(
                        'label' =>  __('Choose Product Category','yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Choose product categories. Leave this field empty if you want all categories to be shown in the slider','yith-banda-product-slider-carousel'),
                        'type'  =>  'ajax-category',
                        'placeholder'   => __('Choose product categories', 'yith-banda-product-slider-carousel'),
                        'multiple' => true,

                    ),

                    'ywcps_product_type'    =>  array(
                        'label' =>  __('Products to show', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Select products to show in the slider: "On Sale", "Best Sellers" etc. or select them manually.', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'select',
                        'options'   =>  array(
                                'on_sale'       =>  __('On Sale', 'yith-banda-product-slider-carousel'),
                                'best_seller'   =>  __('Best Sellers', 'yith-banda-product-slider-carousel'),
                                'free'          =>  __('Free', 'yith-banda-product-slider-carousel'),
                                'last_ins'      =>  __('Last Added', 'yith-banda-product-slider-carousel'),
                                'featured'      =>  __('Featured', 'yith-banda-product-slider-carousel'),
                                'custom_select'   =>  __('Select your product', 'yith-banda-product-slider-carousel')
                                )
                        ),

                    'ywcps_products'	=>	array(
                        'label' =>  __('Choose Product','yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Choose the Products that you want to show in the slider. Leave this field empty if you want all categories to be shown in the slider','yith-banda-product-slider-carousel'),
                        'type'  =>  'ajax-products',
                        'multiple' => true,
                        'std'      =>   array(),
                        'options'   => array(),
                        'id' => 'ajax_ywcps_slider_product',
                        'deps'     => array(
                            'ids'    => '_ywcps_product_type',
                            'values' => 'custom_select',
                        ),
                    ),

                    'ywcps_sep_1'   => array( 'type'=> 'sep' ),

                    'ywcps_title_content_setting'   =>  array( 'type'=>'title', 'desc'=> __('Content Settings', 'yith-banda-product-slider-carousel') ),

                    'ywcps_layout_type' =>  array(
                        'label' =>  __('Slider Template', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Choose a template for your Product Slider', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'select',
                        'options'    =>  array(
                            'default'   =>  'Banda Loop',
                            'tmp1'      =>  'Style 1',
                            'tmp2'      =>  'Style 2',
                            'tmp3'      =>  'Style 3'
                            ),
                        'std' =>    'default'
                    ),

                'ywcp_show_title'   =>  array(
                  'label'   =>  __('Show Title', 'yith-banda-product-slider-carousel'),
                   'desc'   =>  __('Show or Hide Product Slider title', 'yith-banda-product-slider-carousel'),
                    'type'  =>  'checkbox',
                    'std'   =>  1,
                    'default'   =>  1
                ),

                    'ywcps_hide_add_to_cart'    =>  array(
                        'label' =>  __('Hide "Add to cart"', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Hide "Add to cart" in slider', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   =>  0
                    ),

                    'ywcps_hide_price'    =>  array(
                        'label' =>  __('Hide price', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Hide product price in slider', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   =>  0
                    ),

                    'ywcps_image_per_row'   => array(
                        'label' =>  __('Images for row', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  '',
                        'type'  =>  'number',
                        'std'   =>  1,
                        'min'   =>  1,
                        /*'max'   =>  6*/
                        ),

                    'ywcps_order_by'    =>  array(
                        'label'     =>  __('Order By', 'yith-banda-product-slider-carousel'),
                        'type'      =>  'select',
                        'desc'  =>  '',
                        'options'   =>  array(
                            'name'      =>  __('Name', 'yith-banda-product-slider-carousel'),
                            'price'     =>  __('Price', 'yith-banda-product-slider-carousel'),
                            'date'  =>  __('Date', 'yith-banda-product-slider-carousel')
                        ),
                        'deps'     => array(
                            'ids'    => '_ywcps_product_type',
                            'values' => 'on_sale,free,custom_select,featured',
                        ),
                    ),

                    'ywcps_order_type'   => array(
                        'label' =>  __('Order Type', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'select',
                        'desc'  =>  '',
                        'options'   =>  array(
                            'asc'   =>  'ASC',
                            'desc'  =>  'DESC'
                        ),
                        'deps'     => array(
                            'ids'    => '_ywcps_product_type',
                            'values' => 'on_sale,free,custom_select,featured',
                        ),
                    ),
                    'ywcps_sep_2'   => array( 'type'=> 'sep' ),

                    'ywcps_title_slider_setting'   =>  array( 'type'=>'title', 'desc'=> __('Slider Settings', 'yith-banda-product-slider-carousel') ),


                    'ywcps_check_loop' =>  array(
                        'label' =>  __('Loop slider', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Choose if you want your slider to scroll products continuously', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   =>  0,
                        'default'   =>  0
                     ),

                    'ywcps_pagination_speed' =>  array(
                        'label' =>  __('Pagination Speed', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Pagination speed in milliseconds', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'text',
                        'std'   =>  '800',
                        'default'   =>  '800'
                    ),


                    'ywcps_auto_play' =>  array(
                        'label' =>  __('AutoPlay', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Insert the autoplay value in milliseconds, enter 0 to disable it', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'text',
                        'std'   =>  '5000',
                        'default'   =>  '5000'
                    ),

                    'ywcps_stop_hover'  =>  array(
                        'label' =>  __('Stop on Hover', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Stop autoplay on mouse hover', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   => 0
                    ),

                    'ywcps_show_navigation'  =>  array(
                        'label' =>  __('Show Navigation', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Display "prev" and "next" button', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   => 0
                    ),

                    'ywcps_show_dot_navigation' =>  array(
                        'label' =>  __('Show Dots Navigation' ,'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Show or Hide dots navigation', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   =>  0,
                        'default'   => 0
                    ),

                    'ywcps_animate_in'  =>  array(
                        'label' =>  __('Animation IN', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Choose entrance animation for a new slide.<br>*Animation functions work only if there is just one item in the slider and only in browsers that support perspective property', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'select-group',
                        'options'   =>  $animations
                    ),
                    'ywcps_animate_out'  =>  array(
                        'label' =>  __('Animation OUT', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Choose exit animation for a slide.<br>*Animation functions work only if there is just one item in the slider and only in browsers that support perspective property', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'select-group',
                        'options'   =>  $animations
                    ),

                    'ywcps_animation_speed' =>  array(
                        'label' =>  __('Animation Speed', 'yith-banda-product-slider-carousel'),
                        'desc'  =>  __('Enter animation duration in milliseconds', 'yith-banda-product-slider-carousel'),
                        'type'  =>  'text',
                        'std'   =>  450,
                        'default'   => 450
                    )
            ),
        ),
    ),
);

return apply_filters( 'yith_product_slider_metabox', $args );