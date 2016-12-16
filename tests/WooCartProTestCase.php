<?php

require_once dirname(__FILE__).'/../libs/FestiTestCase.php';

class WooCartProTestCase extends FestiTestCase
{

    private $_backend;
    private $_frontend;

    protected $products;
    protected $adminUserId = 1;
    protected $pluginMainFile = false;
    protected $bandaMainFile = false;
    protected $pluginFolderName = 'banda-woocartpro';
    
    public function setUp()
    {
        parent::setUp();
        
        $this->pluginMainFile = $this->getPluginPath('plugin.php');
        
        require_once $this->getPluginPath('config.php');
    } // end setUp
    
    protected function doCreateProduct()
    {
        $wp_error = false;
        
        $post = array(
            'post_author' => $this->adminUserId,
            'post_content' => '',
            'post_status' => 'publish',
            'post_title' => 'Test product',
            'post_parent' => '',
            'post_type' => 'product',
         );
         
          //Create post
         $post_id = wp_insert_post($post, $wp_error);

         wp_set_object_terms($post_id, 'simple', 'product_type');

         update_post_meta($post_id, '_visibility', 'visible');
         update_post_meta($post_id, '_stock_status', 'instock');
         update_post_meta($post_id, 'total_sales', '0');
         update_post_meta($post_id, '_downloadable', 'yes');
         update_post_meta($post_id, '_virtual', 'yes');
         update_post_meta($post_id, '_regular_price', '1');
         update_post_meta($post_id, '_sale_price', '1');
         update_post_meta($post_id, '_purchase_note', '');
         update_post_meta($post_id, '_featured', 'no');
         update_post_meta($post_id, '_weight', '');
         update_post_meta($post_id, '_length', '');
         update_post_meta($post_id, '_width', '');
         update_post_meta($post_id, '_height', '');
         update_post_meta($post_id, '_sku', '');
         update_post_meta($post_id, '_product_attributes', array());
         update_post_meta($post_id, '_sale_price_dates_from', '');
         update_post_meta($post_id, '_sale_price_dates_to', '');
         update_post_meta($post_id, '_price', '1');
         update_post_meta($post_id, '_sold_individually', '');
         update_post_meta($post_id, '_manage_stock', 'no');
         update_post_meta($post_id, '_backorders', 'no');
         update_post_meta($post_id, '_stock', '');
         update_post_meta($post_id, '_download_limit', '');
         update_post_meta($post_id, '_download_expiry', '');
         update_post_meta($post_id, '_download_type', '');
         update_post_meta($post_id, '_product_image_gallery', '');
        
         $this->products['simple']['id'] = $post_id;
    } // end doCreateProduct
    
    protected function getProductId($type)
    {
        return $this->products[$type]['id'];
    } // end getProductId
    
    protected function getBackendInstance()
    {
        if (!$this->_backend) {
            $file = 'common/WooCartBackendFestiPlugin.php';
            require_once $this->getPluginPath($file);
            
            $this->_backend = new WooCartBackendFestiPlugin(
                $this->pluginMainFile
            );
            
            $this->_backend->onInit();
        }
        
        return $this->_backend;
    } // end getBackendInstance
    
    protected function getFrontendInstance()
    {
        if (!$this->_frontend) {
            $file = 'common/WooCartFrontendFestiPlugin.php';
            require_once $this->getPluginPath($file);
    
            $this->_frontend = new WooCartFrontendFestiPlugin(
                $this->pluginMainFile
            );
            
            $this->_frontend->onInit();
        }
    
        return $this->_frontend;
    } // end getFrontendInstance
    
    protected function updateSetting($key, $value)
    {
        $backend = $this->getBackendInstance();
        
        $newSettings = $backend->getSettings();
        $newSettings[$key] = $value;
        
        $settingsFacade = new SettingsWooCartFacade($backend);
        $settingsFacade->sync($newSettings);
        
        return true;
    } // end updateSetting
    
    
    protected function getWpScriptsList()
    {
        global $wp_scripts;
        return $wp_scripts->queue;
    } // end getWpScriptsList
    
    protected function doCleanWpScriptsList()
    {
        global $wp_scripts;
        return $wp_scripts->queue = array();
    } // end doCleanWpScriptsList
    
    protected function getPluginPath($file = '')
    {
        return WP_CONTENT_DIR.'/plugins/'.$this->pluginFolderName.'/'.$file;
    } // end getPluginPath
    
    protected function getBandaPath($file = '')
    {
        return WP_CONTENT_DIR.'/plugins/banda/'.$file;
    } // end getBandaPath

}