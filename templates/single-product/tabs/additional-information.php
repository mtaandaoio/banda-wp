<?php
/**
 * Additional Information tab
 *
 * This template can be overridden by copying it to yourtheme/banda/single-product/tabs/additional-information.php.
 *
 * HOWEVER, on occasion Banda will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.mtaandao.co.ke/document/template-structure/
 * @author        Mtaandao
 * @package       Banda/Templates
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$heading = apply_filters( 'banda_product_additional_information_heading', __( 'Additional Information', 'banda' ) );

?>

<?php if ( $heading ): ?>
	<h2><?php echo $heading; ?></h2>
<?php endif; ?>

<?php $product->list_attributes(); ?>
