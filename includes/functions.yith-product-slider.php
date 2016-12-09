<?php
if( !defined( 'ABSPATH' ) )
    exit;
?>
<?php
if( !function_exists( 'get_tab_categories' ) ) {

    /**get_tab_categories
     *
     * Load all categories in Category chosen field
     * @author YITHEMES
     * @since 1.0.0
     * @return array
     */
     function get_tab_categories() {

        $args = array( 'hide_empty' => 1 );

        $categories_term = get_terms( 'product_cat', $args );

        $categories = array();

        foreach( $categories_term as $category ){

            $categories[ $category->slug ] = '#'. $category->term_id . '-'. $category->name;
        }

        return $categories;

    }

}
if( ! function_exists( 'json_search_product_categories') ) {

    function json_search_product_categories( $x = '', $taxonomy_types = array('product_cat') ) {


        if (isset($_GET['plugin']) && YWCPS_SLUG == $_GET['plugin']) {

            global $wpdb;
            $term = (string)urldecode(stripslashes(strip_tags($_GET['term'])));
            $term = "%" . $term . "%";


            $query_cat = $wpdb->prepare("SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.name LIKE %s", implode(",", $taxonomy_types), $term);

            $product_categories = $wpdb->get_results($query_cat);

            $to_json = array();

            foreach ( $product_categories as $product_category ) {

                $to_json[$product_category->slug] = "#" . $product_category->term_id . "-" . $product_category->name;
            }

           echo json_encode( $to_json );
            die();
        }

    }
}
add_action('wp_ajax_yith_json_search_product_categories',  'json_search_product_categories', 10);


if( !function_exists( 'ywcps_animations_list' ) ){


    function ywcps_animations_list(){

        $animations =   array(
            'Fading Entrances'      =>  array( 'fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig' ),
            'Fading Exits'          =>  array( 'fadeOut','fadeOutDown','fadeOutDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig' )

        );

        return apply_filters( 'ywcps_animate_styles', $animations );
    }
}

if( !function_exists( 'YITH_Product_Slider_Type' ) ){

     function YITH_Product_Slider_Type(){

        if( !defined( 'YWCPS_PREMIUM' ) )
            return YITH_Product_Slider_Type::get_instance();
        else
            return YITH_Product_Slider_Type_Premium::get_instance();
    }
}