<?php
/**
 * My Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/banda/myaccount/my-downloads.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.mtaandao.co.ke/document/template-structure/
 * @author      Mtaandao
 * @package     Banda/Templates
 * @version     2.0.0
 * @depreacated 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $downloads = WC()->customer->get_downloadable_products() ) : ?>

	<?php do_action( 'banda_before_available_downloads' ); ?>

	<h2><?php echo apply_filters( 'banda_my_account_my_downloads_title', __( 'Available Downloads', 'banda' ) ); ?></h2>

	<ul class="banda-Downloads digital-downloads">
		<?php foreach ( $downloads as $download ) : ?>
			<li>
				<?php
					do_action( 'banda_available_download_start', $download );

					if ( is_numeric( $download['downloads_remaining'] ) )
						echo apply_filters( 'banda_available_download_count', '<span class="banda-Count count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $download['downloads_remaining'], 'banda' ), $download['downloads_remaining'] ) . '</span> ', $download );

					echo apply_filters( 'banda_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download );

					do_action( 'banda_available_download_end', $download );
				?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php do_action( 'banda_after_available_downloads' ); ?>

<?php endif; ?>
