<?php
/**
 * Admin Dashboard
 *
 * @author      Mtaandao
 * @category    Admin
 * @package     Banda/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Admin_Dashboard' ) ) :

/**
 * WC_Admin_Dashboard Class.
 */
class WC_Admin_Dashboard {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Only hook in admin parts if the user has admin access
		if ( current_user_can( 'view_banda_reports' ) || current_user_can( 'manage_banda' ) || current_user_can( 'publish_shop_orders' ) ) {
			add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
		}
	}

	/**
	 * Init dashboard widgets.
	 */
	public function init() {
		if ( current_user_can( 'publish_shop_orders' ) ) {
			wp_add_dashboard_widget( 'banda_dashboard_recent_reviews', __( 'Banda Recent Reviews', 'banda' ), array( $this, 'recent_reviews' ) );
		}

		wp_add_dashboard_widget( 'banda_dashboard_status', __( 'Banda Status', 'banda' ), array( $this, 'status_widget' ) );
	}

	/**
	 * Get top seller from DB.
	 * @return object
	 */
	private function get_top_seller() {
		global $wpdb;

		$query            = array();
		$query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
			FROM {$wpdb->posts} as posts";
		$query['join']    = "INNER JOIN {$wpdb->prefix}banda_order_items AS order_items ON posts.ID = order_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}banda_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}banda_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
		$query['where']   = "WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) ";
		$query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'banda_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
		$query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
		$query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
		$query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "' ";
		$query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
		$query['groupby'] = "GROUP BY product_id";
		$query['orderby'] = "ORDER BY qty DESC";
		$query['limits']  = "LIMIT 1";

		return $wpdb->get_row( implode( ' ', apply_filters( 'banda_dashboard_status_widget_top_seller_query', $query ) ) );
	}

	/**
	 * Get sales report data.
	 * @return object
	 */
	private function get_sales_report_data() {
		include_once( dirname( __FILE__ ) . '/reports/class-wc-report-sales-by-date.php' );

		$sales_by_date                 = new WC_Report_Sales_By_Date();
		$sales_by_date->start_date     = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
		$sales_by_date->end_date       = current_time( 'timestamp' );
		$sales_by_date->chart_groupby  = 'day';
		$sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

		return $sales_by_date->get_report_data();
	}

	/**
	 * Show status widget.
	 */
	public function status_widget() {
		include_once( dirname( __FILE__ ) . '/reports/class-wc-admin-report.php' );

		$reports = new WC_Admin_Report();

		echo '<ul class="wc_status_list">';

		if ( current_user_can( 'view_banda_reports' ) && ( $report_data = $this->get_sales_report_data() ) ) {
			?>
			<li class="sales-this-month">
				<a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=orders&range=month' ); ?>">
					<?php echo $reports->sales_sparkline( '', max( 7, date( 'd', current_time( 'timestamp' ) ) ) ); ?>
					<?php printf( __( '<strong>%s</strong> net sales this month', 'banda' ), wc_price( $report_data->net_sales ) ); ?>
				</a>
			</li>
			<?php
		}

		if ( current_user_can( 'view_banda_reports' ) && ( $top_seller = $this->get_top_seller() ) && $top_seller->qty ) {
			?>
			<li class="best-seller-this-month">
				<a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_product&range=month&product_ids=' . $top_seller->product_id ); ?>">
					<?php echo $reports->sales_sparkline( $top_seller->product_id, max( 7, date( 'd', current_time( 'timestamp' ) ) ), 'count' ); ?>
					<?php printf( __( '%s top seller this month (sold %d)', 'banda' ), '<strong>' . get_the_title( $top_seller->product_id ) . '</strong>', $top_seller->qty ); ?>
				</a>
			</li>
			<?php
		}

		$this->status_widget_order_rows();
		$this->status_widget_stock_rows();

		do_action( 'banda_after_dashboard_status_widget', $reports );
		echo '</ul>';
	}

	/**
	 * Show order data is status widget.
	 */
	private function status_widget_order_rows() {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}
		$on_hold_count    = 0;
		$processing_count = 0;

		foreach ( wc_get_order_types( 'order-count' ) as $type ) {
			$counts           = (array) wp_count_posts( $type );
			$on_hold_count    += isset( $counts['wc-on-hold'] ) ? $counts['wc-on-hold'] : 0;
			$processing_count += isset( $counts['wc-processing'] ) ? $counts['wc-processing'] : 0;
		}
		?>
		<li class="processing-orders">
			<a href="<?php echo admin_url( 'edit.php?post_status=wc-processing&post_type=shop_order' ); ?>">
				<?php printf( _n( "<strong>%s order</strong> awaiting processing", "<strong>%s orders</strong> awaiting processing", $processing_count, 'banda' ), $processing_count ); ?>
			</a>
		</li>
		<li class="on-hold-orders">
			<a href="<?php echo admin_url( 'edit.php?post_status=wc-on-hold&post_type=shop_order' ); ?>">
				<?php printf( _n( "<strong>%s order</strong> on-hold", "<strong>%s orders</strong> on-hold", $on_hold_count, 'banda' ), $on_hold_count ); ?>
			</a>
		</li>
		<?php
	}

	/**
	 * Show stock data is status widget.
	 */
	private function status_widget_stock_rows() {
		global $wpdb;

		// Get products using a query - this is too advanced for get_posts :(
		$stock          = absint( max( get_option( 'banda_notify_low_stock_amount' ), 1 ) );
		$nostock        = absint( max( get_option( 'banda_notify_no_stock_amount' ), 0 ) );
		$transient_name = 'wc_low_stock_count';

		if ( false === ( $lowinstock_count = get_transient( $transient_name ) ) ) {
			$query_from = apply_filters( 'banda_report_low_in_stock_query_from', "FROM {$wpdb->posts} as posts
				INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
				INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
				WHERE 1=1
				AND posts.post_type IN ( 'product', 'product_variation' )
				AND posts.post_status = 'publish'
				AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
				AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
				AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'
			" );
			$lowinstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );
			set_transient( $transient_name, $lowinstock_count, DAY_IN_SECONDS * 30 );
		}

		$transient_name = 'wc_outofstock_count';

		if ( false === ( $outofstock_count = get_transient( $transient_name ) ) ) {
			$query_from = apply_filters( 'banda_report_out_of_stock_query_from', "FROM {$wpdb->posts} as posts
				INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
				INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
				WHERE 1=1
				AND posts.post_type IN ( 'product', 'product_variation' )
				AND posts.post_status = 'publish'
				AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
				AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$nostock}'
			" );
			$outofstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );
			set_transient( $transient_name, $outofstock_count, DAY_IN_SECONDS * 30 );
		}
		?>

		<li class="low-in-stock">
			<a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=stock&report=low_in_stock' ); ?>">
				<?php printf( _n( "<strong>%s product</strong> low in stock", "<strong>%s products</strong> low in stock", $lowinstock_count, 'banda' ), $lowinstock_count ); ?>
			</a>
		</li>
		<li class="out-of-stock">
			<a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=stock&report=out_of_stock' ); ?>">
				<?php printf( _n( "<strong>%s product</strong> out of stock", "<strong>%s products</strong> out of stock", $outofstock_count, 'banda' ), $outofstock_count ); ?>
			</a>
		</li>

		<?php
	}

	/**
	 * Recent reviews widget.
	 */
	public function recent_reviews() {
		global $wpdb;
		$comments = $wpdb->get_results( "SELECT *, SUBSTRING(comment_content,1,100) AS comment_excerpt
		FROM $wpdb->comments
		LEFT JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID)
		WHERE comment_approved = '1'
		AND comment_type = ''
		AND post_password = ''
		AND post_type = 'product'
		ORDER BY comment_date_gmt DESC
		LIMIT 8" );

		if ( $comments ) {
			echo '<ul>';
			foreach ( $comments as $comment ) {

				echo '<li>';

				echo get_avatar( $comment->comment_author, '32' );

				$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

				echo '<div class="star-rating" title="' . esc_attr( $rating ) . '">
					<span style="width:' . ( $rating * 20 ) . '%">' . $rating . ' ' . __( 'out of 5', 'banda' ) . '</span></div>';

				echo '<h4 class="meta"><a href="' . get_permalink( $comment->ID ) . '#comment-' . absint( $comment->comment_ID ) .'">' . esc_html( apply_filters( 'banda_admin_dashboard_recent_reviews', $comment->post_title, $comment ) ) . '</a> ' . __( 'reviewed by', 'banda' ) . ' ' . esc_html( $comment->comment_author ) .'</h4>';
				echo '<blockquote>' . wp_kses_data( $comment->comment_excerpt ) . ' [...]</blockquote></li>';

			}
			echo '</ul>';
		} else {
			echo '<p>' . __( 'There are no product reviews yet.', 'banda' ) . '</p>';
		}
	}

}

endif;

return new WC_Admin_Dashboard();
