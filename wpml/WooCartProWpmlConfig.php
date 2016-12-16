<?php

class WooCartProWpmlConfig
{
    protected $pluginName = 'WooCartPro';
    
    public function getWpmlPrefix()
    {
        return $this->pluginName;
    } // end getPluginPrefixName
    
    public function getTranslateList()
    {
        $list = array(
            'festi_cart_settings' => array(
                'textBeforeQuantity',
                'textBeforeQuantityPlural',
                'textAfterQuantity',
                'textAfterQuantityPlural',
                'textBeforeTotal',
                'textAfterTotal',
                'popupHeaderText',
                'popupHeaderTextAlign',
                'popupContinueButtonText',
                'productListEmptyText',
                'productListTotalText',
                'viewCartButtonText',
                'checkoutButtonText',
                'popupHeaderText',
            ),
            'festi_cart_widget_options' => array(
                'title'
            )
        );
        
        return $list;
    }
}
