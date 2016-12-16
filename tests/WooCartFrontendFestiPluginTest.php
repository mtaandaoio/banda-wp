<?php

require_once dirname(__FILE__).'/WooCartProTestCase.php';

class WooCartFrontendFestiPluginTest extends WooCartProTestCase
{
    /**
     * @ticket 2574
     */
    public function testDisableOptionDisplayCartOnAllPages()
    {
        $this->updateSetting('displayCartOnAllPages', "");
        $this->updateSetting('windowCart', 1);

        $frontend = $this->getFrontendInstance();

        $page = $this->createPage();
        $this->setMainPage($page);

        $wp_query = new WP_Query(
            array('posts_per_page' => -1)
        );

        $wp_query->get_posts();

        $footer = $this->doAction('wp_footer');

        $regExp = '#festi-cart-window-content#Umis';
        $this->assertFalse(
            (bool)preg_match($regExp, $footer),
            'Window cart active but we have expected cart disabled'
        );
    } // end testDisableOptionDisplayCartOnAllPages
    
    /**
     * @ticket 2591
     */
    public function testDisplayCartOnCustomChosenPage()
    {
        $this->updateSetting('displayCartOnAllPages', "");
        $this->updateSetting('windowCart', 1);
        
        $page = $this->createPage();
        $this->updateSetting('festiDisplayCartOnPage', array($page->ID));
        
        $frontend = $this->getFrontendInstance();
        
        $permalink = get_permalink($page->ID);

        $this->go_to($permalink);
        
        $query = $GLOBALS['wp_query'];        
        $query->get_posts();
        
        $footer = $this->doAction('wp_footer');

        $regExp = '#festi-cart-window-content#Umis';
        $this->assertTrue(
            (bool) preg_match($regExp, $footer),
            'Cart is not displayed on chosen page'
        );
    } // end testDisplayCartOnCustomChosenPage

    public function testDisplayBadgeQuantityType()
    {
        $this->updateSetting('LocationInCart', 'right');

        $frontend = $this->getFrontendInstance();

        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        $this->assertNotFalse(WC()->cart->add_to_cart($idProduct, 1));

        $this->assertNotFalse(WC()->cart->get_cart_contents_count());

        $fragments = apply_filters("add_to_cart_fragments", array());

        $regExp = "~budgeCounter~";

        $this->assertTrue(
            (bool) preg_match(
                $regExp, $fragments['.festi-cart.festi-cart-widget']
            )
        );
    } // end testDisplayBadgeQuantityType

    public function testDisplayDefaultQuantityType()
    {
        $this->updateSetting('LocationInCart', false);

        $frontend = $this->getFrontendInstance();

        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        $this->assertNotFalse(WC()->cart->add_to_cart($idProduct, 1));

        $this->assertNotFalse(WC()->cart->get_cart_contents_count());

        $fragments = apply_filters("add_to_cart_fragments", array());

        $regExp = "~budgeCounter~";

        $this->assertFalse(
            (bool) preg_match(
                $regExp, $fragments['.festi-cart.festi-cart-widget']
            )
        );

    } // end testDisplayDefaultQuantityType

    
}