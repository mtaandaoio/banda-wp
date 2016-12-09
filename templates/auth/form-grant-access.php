<?php
/**
 * Auth form grant access
 *
 * This template can be overridden by copying it to yourtheme/banda/auth/form-grant-access.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates/Auth
 * @version 2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'banda_auth_page_header' ); ?>

<h1><?php printf( __( '%s would like to connect to your store' , 'banda' ), esc_html( $app_name ) ); ?></h1>

<?php wc_print_notices(); ?>

<p><?php printf( __( 'This will give "%s" <strong>%s</strong> access which will allow it to:' , 'banda' ), esc_html( $app_name ), esc_html( $scope ) ); ?></p>

<ul class="wc-auth-permissions">
	<?php foreach ( $permissions as $permission ) : ?>
		<li><?php echo esc_html( $permission ); ?></li>
	<?php endforeach; ?>
</ul>

<div class="wc-auth-logged-in-as">
	<?php echo get_avatar( $user->ID, 70 ); ?>
	<p><?php printf( __( 'Logged in as %s', 'banda' ), esc_html( $user->display_name ) ); ?> <a href="<?php echo esc_url( $logout_url ); ?>" class="wc-auth-logout"><?php _e( 'Logout', 'banda' ); ?></a>
</div>

<p class="wc-auth-actions">
	<a href="<?php echo esc_url( $granted_url ); ?>" class="button button-primary wc-auth-approve"><?php _e( 'Approve', 'banda' ); ?></a>
	<a href="<?php echo esc_url( $return_url ); ?>" class="button wc-auth-deny"><?php _e( 'Deny', 'banda' ); ?></a>
</p>

<?php do_action( 'banda_auth_page_footer' ); ?>
