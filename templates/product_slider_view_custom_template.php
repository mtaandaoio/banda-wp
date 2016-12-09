<?php
if( !defined( 'ABSPATH' ) )
    exit;

$products   =   get_posts( $query_args );

if( count( $products )>0 ):
$layout='';
$style='';

   switch( $layouts )
   {
       case 'tmp1':
           $layout  =   'ywcps_layout1';
           $style   =   '<style>';
           $style  .=      '#'.$layout.' .ywcps-wrapper .ywcps-slider ul.products li.single_product .single_product_container {
                                background:         '.get_option( $layout.'_box_bg_color', true ).';
                                border-color:       '.get_option( $layout.'_box_border_color', true ).';
                                color:              '.get_option( $layout.'_text_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_name {
                                border-top-color:   '.get_option( $layout.'_box_border_color', true ).';
                                border-bottom-color: '.get_option( $layout.'_box_border_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_name a {
                                color: '.get_option( $layout.'_text_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_add_to_cart,'.'#'.$layout.' a.added_to_cart.wc-forward {
                                background: '.get_option( $layout.'_button_bg_color', true ).';
                                color:      '.get_option( $layout.'_button_color', true ).';
                                border-color:   '.get_option( $layout.'_border_button_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_add_to_cart:hover,'.'#'.$layout.' a.added_to_cart.wc-forward:hover {
                                background: '.get_option( $layout.'_button_bg_color_hover', true ).';
                                color:      '.get_option( $layout.'_button_color_hover', true ).';
                                border-color:   '.get_option( $layout.'_border_button_color_hover', true ).';
                            }';
           $style   .=  '#'.$layout.' .ywcps-wrapper .ywcps-nav-prev #prev_tmp1, #'.$layout.' .ywcps-wrapper .ywcps-nav-next #next_tmp1 {
                                background: '.get_option( $layout.'_background_color_arrow', true ).';
                                color:      '.get_option( $layout.'_text_color_arrow', true ).';
                                border-color:   '.get_option( $layout.'_text_color_arrow', true ).';
                            }';
           $style   .=  '</style>';
        break;
       case 'tmp2':
           $layout  =   'ywcps_layout2';
           $style   =   '<style>';
           $style  .=      '#'.$layout.' .ywcps-wrapper .ywcps-slider {
                                background:         '.get_option( $layout.'_box_bg_color', true ).';
                                border-color:       '.get_option( $layout.'_box_border_color', true ).';
                                color:              '.get_option( $layout.'_text_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .ywcps-wrapper .ywcps-slider ul.products .owl-item{
                                border-left-color:   '.get_option( $layout.'_box_border_color', true ).';

                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_name a {
                                color: '.get_option( $layout.'_text_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_add_to_cart,'.'#'.$layout.' a.added_to_cart.wc-forward {
                                background: '.get_option( $layout.'_button_bg_color', true ).';
                                color:      '.get_option( $layout.'_button_color', true ).';
                                border-color:   '.get_option( $layout.'_border_button_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_add_to_cart:hover,'.'#'.$layout.' a.added_to_cart.wc-forward:hover {
                                background: '.get_option( $layout.'_button_bg_color_hover', true ).';
                                color:      '.get_option( $layout.'_button_color_hover', true ).';
                                border-color:   '.get_option( $layout.'_border_button_color_hover', true ).';
                            }';
           $style   .=  '#'.$layout.' .ywcps-wrapper .ywcps-nav-prev #prev_tmp2, #'.$layout.' .ywcps-wrapper .ywcps-nav-next #next_tmp2 {
                                background: '.get_option( $layout.'_background_color_arrow', true ).';
                                border-color:      '.get_option( $layout.'_border_color_arrow', true ).';
                                color:   '.get_option( $layout.'_text_color_arrow', true ).';
                            }';
           $style   .=  '</style>';
           break;
       case 'tmp3':
           $layout  =   'ywcps_layout3';
           $style   =   '<style>';
           $style  .=      '#'.$layout.' .ywcps-wrapper .ywcps-slider {
                                background:         '.get_option( $layout.'_box_bg_color', true ).';
                                border-color:       '.get_option( $layout.'_box_border_color', true ).';
                                color:              '.get_option( $layout.'_text_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .ywcps-wrapper .ywcps-slider ul.products .owl-item{
                                border-left-color:   '.get_option( $layout.'_box_border_color', true ).';

                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_name a {
                                color: '.get_option( $layout.'_text_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_add_to_cart, #'.$layout.' .single_product_container .product_other_info .product_add_to_cart:hover i, #'.$layout.' a.added_to_cart.wc-forward {
                                background: '.get_option( $layout.'_button_bg_color', true ).';
                                color:      '.get_option( $layout.'_button_color', true ).';
                                border-color:   '.get_option( $layout.'_border_button_color', true ).';
                            }';
           $style   .=  '#'.$layout.' .single_product_container .product_other_info .product_add_to_cart:hover, #'.$layout.' .single_product_container .product_other_info .product_add_to_cart:hover i, #'.$layout.' a.added_to_cart.wc-forward:hover {
                                background: '.get_option( $layout.'_button_bg_color_hover', true ).';
                                color:      '.get_option( $layout.'_button_color_hover', true ).';
                                border-color:   '.get_option( $layout.'_border_button_color_hover', true ).';
                            }';

           $style   .=  '#'.$layout.' .ywcps-wrapper .ywcps-nav-prev , #'.$layout.' .ywcps-wrapper .ywcps-nav-next  {
                                background: '.get_option( $layout.'_background_color_arrow', true ).';
                                border-color:      '.get_option( $layout.'_border_color_arrow', true ).';
                                color:   '.get_option( $layout.'_text_color_arrow', true ).';
                            }';
           $style   .=  '</style>';
           break;
   }

    $z_index = empty( $z_index )? '' : 'style="z-index: '.$z_index.';"';

    echo $style;

    echo '<div id="'.$layout.'" class="general_container banda">';
    if( $show_title )
        echo    '<h3>'.get_the_title( $id ).'</h3>';
    echo    '<div class="ywcps-wrapper"  data-en_responsive="'.$en_responsive.'" data-n_item_desk_small="'.$n_item_desk_small.'" data-n_item_tablet="'.$n_item_tablet.'" data-n_item_mobile="'.$n_item_mobile.'"';
    echo   'data-n_items="'.$n_items.'" data-is_loop="'.$is_loop.'" data-pag_speed="'.$page_speed.'" data-auto_play="'.$auto_play.'" data-stop_hov="'.$stop_hov.'" data-show_nav="'.$show_nav.'"';
    echo   'data-en_rtl="'.$is_rtl.'" data-anim_in="'.$anim_in.'" data-anim_out="'.$anim_out.'" data-anim_speed="'.$anim_speed.'" data-show_dot_nav="'.$show_dot_nav.'">';
    echo    '<div class="ywcps-slider" style="visibility: hidden;">';
    echo    '<div class="row">';
    echo        '<ul class="ywcps-products products '.$layout.'"'. $z_index.'>';
    foreach( $products as $prod ) {

        if( function_exists( 'get_product' ) )
            $product    =   get_product( $prod->ID );
        else
            $product    =   wc_get_product( $prod->ID );

        echo    '<li class="single_product product">';
        echo        '<div class="single_product_container product-wrapper">';
        $img='';
        if( has_post_thumbnail( $prod->ID ) )
            $img  =   get_the_post_thumbnail( $prod->ID, 'shop_catalog' );
        else
            $img    =   wc_placeholder_img('shop_catalog');
        echo            '<div class="product_img">';
        echo                '<a href="'.get_permalink( $prod->ID ).'" >'.$img.'</a>';
        echo            '</div>';
        echo            '<div class="product_other_info">';
        echo                '<div class="product_name"><a href="'.get_permalink( $prod->ID ).'" >'.$prod->post_title.'</a>';
        if( !$hide_price )
            echo                    '<div class="product_price">'.$product->get_price_html().'</div>';
        echo                '</div>';
        if( !$hide_add_to_cart ) {

            $add_to_cart_text = $product->add_to_cart_text();
            $add_to_cart_url = $product->add_to_cart_url();
            $defaults = array(
                'quantity' => 1,
                'class'    => implode( ' ', array_filter( array(
                    'button',
                    'product_type_' . $product->product_type,
                    $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                    $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
                ) ) )
            );
            
            $add_to_cart_button ='';
            switch( $layouts )
            {
                case 'tmp1':

                    $add_to_cart_button = sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s"><span class="icon_add_to_cart">&nbsp;</span>%s</a>',
                       $add_to_cart_url,1, $product->id, $product->get_sku(), $defaults['class'], $add_to_cart_text );
                    break;
                case 'tmp2':
                    $add_to_cart_button = sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
                        $add_to_cart_url,1, $product->id, $product->get_sku(), $defaults['class'], $add_to_cart_text );
                    break;
                case 'tmp3':
                    $add_to_cart_button = sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">+ %s</a>',
                        $add_to_cart_url,1, $product->id, $product->get_sku(), $defaults['class'], $add_to_cart_text );
                    break;

            }
            echo $add_to_cart_button;
        }
        echo            '</div>';
        echo        '</div>';
        echo    '</li>';
    }
    echo        '</ul>';
    echo        '</div></div>';
    echo    '<div class="ywcps-nav" id="nav_'.$layouts.'">';
    echo      '<div id="nav_prev_'.$id.'" class="ywcps-nav-prev"><span id="prev_'.$layouts.'" class="fa fa-chevron-left"></span></div>';
    echo      '<div id="nav_next_'.$id.'" class="ywcps-nav-next"><span id="next_'.$layouts.'" class="fa fa-chevron-right"></span></div>';
    echo    '</div>';
    echo    '</div>';

    echo '</div>';


endif;

wp_reset_postdata();