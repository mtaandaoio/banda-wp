<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/review.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.mtaandao.co.ke/document/template-structure/
 * @author  Mtaandao
 * @package Banda/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<?php
		/**
		 * The banda_review_before hook
		 *
		 * @hooked banda_review_display_gravatar - 10
		 */
		do_action( 'banda_review_before', $comment );
		?>

		<div class="comment-text">

			<?php
			/**
			 * The banda_review_before_comment_meta hook.
			 *
			 * @hooked banda_review_display_rating - 10
			 */
			do_action( 'banda_review_before_comment_meta', $comment );

			/**
			 * The banda_review_meta hook.
			 *
			 * @hooked banda_review_display_meta - 10
			 */
			do_action( 'banda_review_meta', $comment );

			do_action( 'banda_review_before_comment_text', $comment );

			/**
			 * The banda_review_comment_text hook
			 *
			 * @hooked banda_review_display_comment_text - 10
			 */
			do_action( 'banda_review_comment_text', $comment );

			do_action( 'banda_review_after_comment_text', $comment ); ?>

		</div>
	</div>
