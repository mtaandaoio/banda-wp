<?php
/**
 * Order Tracking Shortcode
 *
 * Lets a user see the status of an order by entering their order details.
 *
 * @author 		Mtaandao
 * @category 	Shortcodes
 * @package 	Banda/Shortcodes/Order_Tracking
 * @version     2.3.0
 */
class WC_Shortcode_Order_Tracking {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		// Check cart class is loaded or abort
		if ( is_null( WC()->cart ) ) {
			return;
		}

		extract(shortcode_atts(array(
		), $atts));

		global $post;

		if ( ! empty( $_REQUEST['orderid'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'banda-order_tracking' ) ) {

			$order_id 		= empty( $_REQUEST['orderid'] ) ? 0 : esc_attr( $_REQUEST['orderid'] );
			$order_email	= empty( $_REQUEST['order_email'] ) ? '' : esc_attr( $_REQUEST['order_email']) ;

			if ( ! $order_id ) {

				echo '<p class="banda-error">' . __( 'Please enter a valid order ID', 'banda' ) . '</p>';

			} elseif ( ! $order_email ) {

				echo '<p class="banda-error">' . __( 'Please enter a valid order email', 'banda' ) . '</p>';

			} else {

				$order = wc_get_order( apply_filters( 'banda_shortcode_order_tracking_order_id', $order_id ) );

				if ( $order && $order->id && $order_email ) {

					if ( strtolower( $order->billing_email ) == strtolower( $order_email ) ) {
						do_action( 'banda_track_order', $order->id );
						wc_get_template( 'order/tracking.php', array(
							'order' => $order
						) );

						return;
					}

				} else {

					echo '<p class="banda-error">' . sprintf( __( 'Sorry, we could not find that order ID in our database.', 'banda' ), get_permalink($post->ID ) ) . '</p>';

				}

			}

		}

		wc_get_template( 'order/form-tracking.php' );
	}
}
