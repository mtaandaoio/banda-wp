<?php

class WooCartFestiWidget extends WP_Widget
{
    private $_plugin;
    private $_languageDomain = '';

    function __construct() 
    {    
        $this->_plugin = &$GLOBALS['wooCommerceFestiCart'];
        
        $this->_languageDomain = $this->_plugin->getLanguageDomain();
        
        parent::__construct(
            WOOCARTPRO_CART_WIDGET_ID,
            __('Banda WooCart Pro', $this->_languageDomain),
            array(
                'description' => __(
                    'Banda WooCart Pro - vvvplocker..com', 
                    $this->_languageDomain
                )
            )
        );
    } // end __consrtuct
    
    public function loadWidgetOptions()
    {
        $options = array(
            'title' => array(
                'caption' => __('Title', $this->_languageDomain),
                'type' => 'input_text',
                'default' => 'Cart'
            ),
            'displayAsList' => array(
                'lable' => __(
                    'Display as a list of products',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox'     
            ),
            'hideCart' => array(
                'lable' => __(
                    'Hide if cart is empty',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox'     
            )
        );

        $values = $this->_plugin->getOptions('widget_options');
        if ($values) {
            foreach ($options as $ident => &$item) {
                if (array_key_exists($ident, $values)) {
                    $item['value'] = $values[$ident];
                }
            }
            unset($item);
        }
        
        return $options;
    } // end loadWidgetOptions

    public function display($params)
    {
        $vars = array (
            'params'  => $params,
            'options' => $this->_plugin->getOptions('widget_options'),
            'banda' => $this->_plugin->getBandaInstance()
        );
        
        echo $this->_plugin->fetch('frontend/widget.phtml', $vars);
    } // end widget
    
    public function isUpdateWidgetOptions()
    {
        return array_key_exists('__action', $_POST);
    } // end isUpdateOptionsPage

    public function form($instance)
    {
        $options = $this->_plugin->getOptions('widget_options'); 
        
        $vars = array(
            'currentValues' => $options,
            'widgetOptions' => $this->loadWidgetOptions()
        );
        
        echo $this->_plugin->fetch('backend/widget.phtml', $vars);
    } // end form

    public function update($new_instance, $old_instance)
    {
        if ($this->isUpdateWidgetOptions()) {
            $options = array(
                'title' => $_POST['title'],
            );
            
            if ($this->_hasHideCartOptionInRequest()) {
                $options['hideCart'] = $_POST['hideCart'];
            }
            
            if ($this->_hasProductListCartOptionInRequest()) {
                $options['displayAsList'] = $_POST['displayAsList'];
            }

            $this->_plugin->updateOptions('widget_options', $options);
        }
       
        return $instance;
    } // end update
    
    private function _hasHideCartOptionInRequest()
    {
        return array_key_exists('hideCart', $_POST) 
               && !empty($_POST['hideCart']);
    } // end _hasIconColorInRequest
    
    private function _hasProductListCartOptionInRequest()
    {
        return array_key_exists('displayAsList', $_POST) 
               && !empty($_POST['displayAsList']);
    } // end _hasIconColorInRequest
    
    /* $widgetParams = 1 - need for compability with abstract class*/
    public function display_callback($params, $widgetParams = 1)
    {
        $this->display($params);
    }
}