<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YWCPS_FREE_INIT', plugin_basename( __FILE__ ) );


if ( !defined( 'YWCPS_VERSION' ) ) {
    define( 'YWCPS_VERSION', '1.0.12' );
}

if ( ! defined( 'YWCPS_PREMIUM' ) ) {
    define( 'YWCPS_PREMIUM', '1' );
}

if ( !defined( 'YWCPS_INIT' ) ) {
    define( 'YWCPS_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWCPS_FILE' ) ) {
    define( 'YWCPS_FILE', __FILE__ );
}

if ( !defined( 'YWCPS_DIR' ) ) {
    define( 'YWCPS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWCPS_URL' ) ) {
    define( 'YWCPS_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWCPS_ASSETS_URL' ) ) {
    define( 'YWCPS_ASSETS_URL', YWCPS_URL . 'assets/' );
}

if ( !defined( 'YWCPS_ASSETS_PATH' ) ) {
    define( 'YWCPS_ASSETS_PATH', YWCPS_DIR . 'assets/' );
}

if ( !defined( 'YWCPS_TEMPLATE_PATH' ) ) {
    define( 'YWCPS_TEMPLATE_PATH', YWCPS_DIR . 'templates/' );
}

if ( !defined( 'YWCPS_INC' ) ) {
    define( 'YWCPS_INC', YWCPS_DIR . 'includes/' );
}

if( !defined( 'YWCPS_SLUG' ) ){
    define( 'YWCPS_SLUG', 'yith-banda-product-slider-carousel' );
}

if ( ! defined( 'YWCPS_SECRET_KEY' ) ) {
    define( 'YWCPS_SECRET_KEY', '0jNOcvP8O85GJDnncahG' );
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


function yith_ywcps_premium_install_banda_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH Banda Product Slider Carousel Premium is enabled but not effective. It requires Banda in order to work.', 'yith-banda-product-slider-carousel' ); ?></p>
        </div>
    <?php
    }

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCPS_DIR . 'plugin-fw/init.php' ) )
    require_once( YWCPS_DIR . 'plugin-fw/init.php' );

yit_maybe_plugin_fw_loader( YWCPS_DIR  );



if ( ! function_exists( 'YITH_Product_Slider_Premium_Init' ) ) {
    /**
     * Unique access to instance of YITH_Product_Slider class
     *
     * @return YITH_Product_Slider_Premium
     * @since 1.0.3
     */
    function YITH_Product_Slider_Premium_Init() {

        load_plugin_textdomain( 'yith-banda-product-slider-carousel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Load required classes and functions
        require_once( YWCPS_INC .'functions.yith-product-slider.php' );
        require_once( YWCPS_INC .'class.yith-product-slider-type.php' );
        require_once( YWCPS_INC .'class.yith-product-slider-shortcode.php' );
        require_once( YWCPS_INC .'class.yith-banda-product-slider.php' );
        require_once( YWCPS_INC .'functions.yith-product-slider-premium.php' );
        require_once( YWCPS_INC .'class.yith-product-slider-type-premium.php' );
        require_once( YWCPS_INC .'class.yith-banda-product-slider-premium.php' );
        require_once( YWCPS_INC .'class.yith-product-slider-widget.php' );

        global $YWC_Product_Slider;

        $YWC_Product_Slider = YITH_Banda_Product_Slider_Premium::get_instance();


    }
}

add_action( 'ywcps_premium_init', 'YITH_Product_Slider_Premium_Init' );

if( !function_exists( 'yith_product_slider_carousel_premium_install' ) ){

    function yith_product_slider_carousel_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_ywcps_premium_install_banda_admin_notice' );

        }else
            do_action( 'ywcps_premium_init' );
    }
}

add_action( 'plugins_loaded', 'yith_product_slider_carousel_premium_install' ,11 );