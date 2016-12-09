<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/banda/loop/result-count.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author 		Mtaandao
 * @package 	Banda/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

if ( ! banda_products_will_display() )
	return;
?>
<p class="banda-result-count">
	<?php
	$paged    = max( 1, $wp_query->get( 'paged' ) );
	$per_page = $wp_query->get( 'posts_per_page' );
	$total    = $wp_query->found_posts;
	$first    = ( $per_page * $paged ) - $per_page + 1;
	$last     = min( $total, $wp_query->get( 'posts_per_page' ) * $paged );

	if ( $total <= $per_page || -1 === $per_page ) {
		printf( _n( 'Showing the single result', 'Showing all %d results', $total, 'banda' ), $total );
	} else {
		printf( _nx( 'Showing the single result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, '%1$d = first, %2$d = last, %3$d = total', 'banda' ), $first, $last, $total );
	}
	?>
</p>
