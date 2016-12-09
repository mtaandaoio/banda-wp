<?php
if ( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_Banda_Product_Slider_Premium' ) ){

    class YITH_Banda_Product_Slider_Premium extends  YITH_Banda_Product_Slider
    {



        public function __construct()
        {
            parent::__construct();

            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
            add_filter( 'ywcps_animate_styles', array( $this, 'add_animation_premium' ), 10,1 );
            add_action( 'admin_init', array( $this, 'add_free_slider_in_table' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_premium_style_script' ),20 );


        }


        /**Returns single instance of the class
         * @author YITHEMES
         * @since 1.0.0
         * @return YITH_Banda_Product_Slider_Premium
         */
        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }



        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once YWCPS_DIR.'plugin-fw/licence/lib/yit-licence.php';
                require_once YWCPS_DIR.'plugin-fw/licence/lib/yit-plugin-licence.php';
            }

            YIT_Plugin_Licence()->register(YWCPS_INIT, YWCPS_SECRET_KEY, YWCPS_SLUG);
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if ( !class_exists( 'YIT_Upgrade' ) ) {
                require_once(YWCPS_DIR.'plugin-fw/lib/yit-upgrade.php');
            }

            YIT_Upgrade()->register( YWCPS_SLUG, YWCPS_INIT );
        }


        /**return all product slider
         * @author YITHEMES
         * @since 1.0.0
         * @used include_style_script
         * @return array
         */
        public function get_productslider()
        {
            $query      =   array(
                'post_per_page' =>  -1,
                'post_type'     =>  'yith_wcps_type',
                'post_status'   =>  'publish',
                'orderby'       =>  'title',
                'order'         =>  'ASC',
                'suppress_filters'  =>  0
            );

            $product_sliders    =   get_posts( $query );
            $to_script = array();
            foreach( $product_sliders as $slider )
                $to_script[] = array( 'text' => $slider->post_title, 'value' => $slider->ID );

            return $to_script;

        }

        public function add_animation_premium( $animations )
        {
            $new_animation  =   array(
                'Attention Seekers'     =>  array( 'bounce','flash','pulse','rubberBand','shake','swing','tada','wobble','jello' ),
                'Bouncing Entrances'    =>  array( 'bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp' ),
                'Bouncing Exits'        =>  array( 'bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp' ),
                'Flippers'              =>  array( 'flip','flipInX','flipInY','flipOutX','flipOutY' ),
                'Lightspeed'            =>  array( 'lightSpeedIn', 'lightSpeedOut' ),
                'Rotating Entrances'    =>  array( 'rotateIn', 'rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight' ),
                'Rotating Exits'        =>  array( 'rotateOut', 'rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight' ),
                'Sliding Entrances'     =>  array( 'slideInUp', 'slideInDown', 'slideInLeft', 'slideInRight' ),
                'Sliding Exits'         =>  array( 'slideOutUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight' ),
                'Zoom Entrances'        =>  array( 'zoomIn', 'zoomInDown', 'zoomInUp','zoomInLeft','zoomInRight' ),
                'Zoom Exits'            =>  array( 'zoomOut', 'zoomOutDown', 'zoomOutUp','zoomOutLeft','zoomOutRight' ),
                'Specials'              =>  array( 'hinge','rollIn','rollOut' )

            ) ;

            return array_merge( $animations, $new_animation );
        }

        public function add_free_slider_in_table(){

          if( 'yes' == get_option('_ywcps_first_install', 'yes' ) ) {
              global $wpdb;

              $id = $wpdb->get_var("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key= '_ywcps_free_slider_id' LIMIT 1");


              if ( $id==null )
                  return;

              $title = get_option('ywcps_title');

              $my_post = array();
              $my_post['ID'] = intval($id);
              $my_post['post_status'] = 'publish';
              $my_post['post_title'] = $title;

              wp_update_post($my_post);

              /*Set postmeta for free slider*/

              $categories   = get_option( 'ywcps_categories' );

              $n_items      = get_option( 'ywcps_image_per_row' );
              $order_by     = get_option( 'ywcps_order_by' );
              $order        = get_option( 'ywcps_order_type' );
              $is_loop      = get_option( 'ywcps_check_loop' ) == 'yes' ? 1 : 0;
              $page_speed   = get_option( 'ywcps_pagination_speed' );
              $auto_play    = get_option( 'ywcps_auto_play' );
              $stop_hov     = get_option( 'ywcps_stop_hover' ) == 'yes' ? 1 : 0;
              $show_nav     = get_option( 'ywcps_show_navigation' ) == 'yes' ? 1 : 0;
              $anim_in      = get_option( 'ywcps_animate_in' );
              $anim_out     = get_option( 'ywcps_animate_out' );
              $anim_speed   = get_option( 'ywcps_animation_speed' );
              $show_dot_nav = get_option( 'ywcps_show_dot_navigation' ) == 'yes' ? 1 : 0;
              $show_title   = get_option( 'ywcps_show_title' ) == 'yes';

              update_post_meta( $id, '_ywcps_categories', $categories );
              update_post_meta( $id, '_ywcp_show_title', $show_title );
              update_post_meta( $id, '_ywcps_image_per_row', $n_items );
              update_post_meta( $id, '_ywcps_order_by', $order_by );
              update_post_meta( $id, '_ywcps_order_type', $order );
              update_post_meta( $id, '_ywcps_check_loop', $is_loop );
              update_post_meta( $id, '_ywcps_pagination_speed', $page_speed );
              update_post_meta( $id, '_ywcps_auto_play', $auto_play );
              update_post_meta( $id, '_ywcps_stop_hover', $stop_hov );
              update_post_meta( $id, '_ywcps_show_navigation', $show_nav );
              update_post_meta( $id, '_ywcps_animate_in', $anim_in );
              update_post_meta( $id, '_ywcps_animate_out', $anim_out );
              update_post_meta( $id, '_ywcps_animation_speed', $anim_speed );
              update_post_meta( $id, '_ywcps_show_dot_navigation', $show_dot_nav );
              update_post_meta( $id, '_ywcps_layout_type', 'default' );

              update_option('_ywcps_first_install', 'no' );

          }

        }

        public function include_admin_premium_style_script()
        {
            global $pagenow;

            $posttype_now   = get_current_screen()->post_type;

            if( ('post.php' == $pagenow )|| ('post-new.php' == $pagenow )  && 'yith_wcps_type'== $posttype_now )
            {
                wp_register_style( 'ywcps_admin_style', YWCPS_ASSETS_URL.'css/product_slider_admin.css', array(), YWCPS_VERSION );
                wp_enqueue_style( 'ywcps_admin_style' );
            }
            
        }

    }
}