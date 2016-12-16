<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_Product_Slider_Shortcode' ) ){

    class YITH_Product_Slider_Shortcode{

        public static function print_product_slider( $atts, $content=null ){

            global $banda_loop;

            $default_attrs  = array(
                'id'    =>  '',
                'z_index' => ''
            );

            $atts    =   shortcode_atts( $default_attrs, $atts );

            extract( $atts );


            $id  =   intval( yith_wpml_get_translated_id( $id, 'yith_wcps_type' ) );


           if( isset( $id ) && !empty( $id ) ){


               $extra_params    =   array(
               'en_responsive'       =>   get_option( 'ywcps_check_responsive' ) == 'yes' ? "true" :  "false" ,
               'n_item_desk_small'   =>   get_option( 'ywcps_n_item_small_desk' ),
               'n_item_tablet'       =>   get_option( 'ywcps_n_item_tablet' ),
               'n_item_mobile'       =>   get_option( 'ywcps_n_item_mobile' ),
               'is_rtl'              =>   get_option( 'ywcps_check_rtl' ) == 'yes'  ?   "true"    :   "false",
               'posts_per_page'      =>    get_option('ywcps_n_posts_per_page'),
               //Slider Settings
               'title'               =>   get_the_title( $id ),
               'categories'          =>   get_post_meta( $id, '_ywcps_categories' ,true ),
               'product_type'        =>   get_post_meta( $id, '_ywcps_product_type', true ),
               'show_title'          =>   get_post_meta( $id, '_ywcp_show_title', true) == 1,
               'hide_add_to_cart'    =>   get_post_meta( $id, '_ywcps_hide_add_to_cart',true ) == 1 ,
               'hide_price'          =>   get_post_meta( $id, '_ywcps_hide_price',true )   ==   1 ,
               'n_items'             =>   get_post_meta( $id, '_ywcps_image_per_row', true ),
               'order_by'            =>   get_post_meta( $id, '_ywcps_order_by',true ),
               'order'               =>   get_post_meta( $id, '_ywcps_order_type',true ),
               'is_loop'             =>   get_post_meta( $id, '_ywcps_check_loop',true ) == 1 ?  "true"  :   "false",
               'page_speed'          =>   get_post_meta( $id, '_ywcps_pagination_speed',true ),
               'auto_play'           =>   get_post_meta( $id, '_ywcps_auto_play', true ) ,
               'stop_hov'            =>   get_post_meta( $id, '_ywcps_stop_hover', true )   ==  1 ?   "true"    :   "false",
               'show_nav'            =>   get_post_meta( $id, '_ywcps_show_navigation', true )   == 1 ?   "true"    :   "false",
               'anim_in'             =>   get_post_meta( $id, '_ywcps_animate_in', true ),
               'anim_out'            =>   get_post_meta( $id, '_ywcps_animate_out', true ),
               'anim_speed'          =>   get_post_meta( $id, '_ywcps_animation_speed', true ),
               'show_dot_nav'        =>   get_post_meta( $id, '_ywcps_show_dot_navigation', true ) == 1 ? "true"    :   "false",
               'template_slider'     =>   get_post_meta( $id, '_ywcps_layout_type', true )
           );
               $atts['id']  =   $id;
               $atts[ 'z_index' ] = $z_index;
               $atts = array_merge( $extra_params, $atts );
               $atts[ 'atts' ] = $atts;

               $old_banda_loop = $banda_loop;
               /**
                * Since 1.0.5
                *
                * @param $banda_loop mixed Banda loop global
                * @param $plugin_slug string Current plugin slug
                */

               $banda_loop = apply_filters( 'yith_customize_product_carousel_loop', $banda_loop, YWCPS_SLUG );

               ob_start();
               wc_get_template( 'product_slider_view.php', $atts, '', YWCPS_TEMPLATE_PATH );
               $template = ob_get_contents();
               ob_end_clean();
               $banda_loop = $old_banda_loop;

               return apply_filters( 'yith_wcpsl_productslider_html', $template, array(), true );

           }

        }
    }
}
add_shortcode( 'yith_wc_productslider', array( 'YITH_Product_Slider_Shortcode', 'print_product_slider' ) );