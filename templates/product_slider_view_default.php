<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}
?>
<?php

global $wpdb, $banda, $banda_loop;

$products = new WP_Query( $query_args );
$i = 0;
$cols = '';

$priorities    =   array(
    'hide_cart'     =>  -1,
    'hide_price'    =>  -1
);


if( $hide_add_to_cart )
{
    $priorities['hide_cart'] = has_action( 'banda_after_shop_loop_item', 'banda_template_loop_add_to_cart' );

    if( $priorities['hide_cart'] != false )
    {
        remove_action( 'banda_template_loop_add_to_cart', $priorities['hide_cart'] );
        add_filter( 'banda_loop_add_to_cart_link', '__return_empty_string', 10 );
    }

}

if( $hide_price ){

    $priorities['hide_price'] = has_action( 'banda_after_shop_loop_item_title', 'banda_template_loop_price' );

    if( $priorities['hide_price'] != false )
    {
        remove_action( 'banda_template_loop_price', $priorities['hide_price'] );
        add_filter( 'banda_get_price_html', '__return_empty_string', 10 );

    }
}

$extra_class = isset( $banda_loop['products_layout'] ) ? array( $banda_loop['products_layout'] ) : array();

$extra_class = apply_filters( 'ywcps_add_classes_in_slider',  $extra_class );

$extra_class = implode(' ', $extra_class );
$z_index = empty( $z_index )? '' : 'style="z-index: '.$z_index.';"';

ob_start();

if ( $products->have_posts() ) :
    echo '<div class="banda ywcps-product-slider">';
    if( $show_title )
        echo    '<h3>'.get_the_title( $id ).'</h3>';
    echo '<div class="ywcps-wrapper" data-columns="%columns%" data-en_responsive="'.$en_responsive.'" data-n_item_desk_small="'.$n_item_desk_small.'" data-n_item_tablet="'.$n_item_tablet.'" data-n_item_mobile="'.$n_item_mobile.'"';
    echo   'data-n_items="'.$n_items.'" data-is_loop="'.$is_loop.'" data-pag_speed="'.$page_speed.'" data-auto_play="'.$auto_play.'" data-stop_hov="'.$stop_hov.'" data-show_nav="'.$show_nav.'"';
    echo   'data-en_rtl="'.$is_rtl.'" data-anim_in="'.$anim_in.'" data-anim_out="'.$anim_out.'" data-anim_speed="'.$anim_speed.'" data-show_dot_nav="'.$show_dot_nav.'">';
    echo '<div class="ywcps-slider '.$extra_class.'" style="visibility:hidden;">';
    echo'<ul class="ywcps-products products ywcps_products_slider" '.$z_index.'>';
    while ( $products->have_posts() ) : $products->the_post();
        wc_get_template( 'content-product.php' );
        $i ++;
        $cols = ( isset($banda_loop['columns']) ) ? $banda_loop['columns'] : 6; //fix $banda_loop['columns'] empty
    endwhile; // end of the loop.
    echo '</ul></div>';
    echo '<div class="ywcps-nav">';
    echo '<div id="nav_prev_def_'.$id.'" class="ywcps-nav-prev"><span id="default_prev"></span></div>';
    echo '<div id="nav_next_def_'.$id.'" class="ywcps-nav-next"><span id="default_next"></span></div>';
    echo '</div></div><div class="es-carousel-clear"></div>';
    echo '</div>';
endif;

if( $hide_add_to_cart && $priorities['hide_cart'] != false )
{
    add_action( 'banda_template_loop_add_to_cart', $priorities['hide_cart'] );
    remove_filter( 'banda_loop_add_to_cart_link', '__return_empty_string', 10 );
}

if ( $hide_price && $priorities['hide_price'] != false ) {
    add_action( 'banda_template_loop_price', $priorities['hide_price'] );
    remove_filter( 'banda_get_price_html', '__return_empty_string', 10 );
}

$content = ob_get_clean();

echo str_replace( '%columns%', $cols, $content );

wp_reset_query();
wp_reset_postdata();
