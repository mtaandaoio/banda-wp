<?php

if (!interface_exists('IFestiWpmlTranslator')) {
    require_once dirname(__FILE__).'/IFestiWpmlTranslator.php';
}

class FestiWpmlTranslator implements IFestiWpmlTranslator
{
    protected $mainFile;
    protected $config;
    
    public function __construct($pluginNamePrefix, $mainFile)
    {
        $this->mainFile = $mainFile;
        $this->config = $this->factory($pluginNamePrefix);
        
        $this->onInit();
    } // end __construct
    
    protected function factory($pluginNamePrefix)
    {
        $className = $pluginNamePrefix.'WpmlConfig';
        
        $classFile = dirname($this->mainFile).'/wpml/'.$className.'.php';
                          
        if (!file_exists($classFile)) {
            throw new Exception(
                sprintf(
                    'File "%s" for "%s" was not found.',
                    $classFile,
                    $className
                )
            );
        }
            
        require_once $classFile;
        
        if (!class_exists($className)) {
            throw new Exception(
                sprintf(
                    'Class "%s" was not found in file "%s".',
                    $className,
                    $classFile
                )
            );
        }
        
        return new $className();
    } // end factory
    
    protected function onInit()
    {
        add_filter(
            'festi_plugin_get_options',
            array($this, 'onGetWpmlStringsFilter'),
            10,
            2
        );

        add_action(
            'festi_plugin_update_options',
            array($this, 'onRegisterWpmlStringsFilter'),
            10,
            2
        );
    } // end onInit
    
    public function onGetWpmlStringsFilter($options, $optionName)
    {
        if (defined('WP_BLOG_ADMIN') || !function_exists('icl_translate')) {
            return $options;
        }

        $wpmlPluginName = $this->getWpmlPrefix(); 
        $stringsList = $this->getTranslateList();
        
        if (!array_key_exists($optionName, $stringsList) ) {
            return $options;
        }
        
        $stringsList = $stringsList[$optionName];
        
        if (is_string($options) && is_string($stringsList)) {
            icl_translate(
                $wpmlPluginName,
                $stringsList,
                $options
            );
            
            return $options;
        }
        
        foreach ($options as $key => $value) {
            if (!is_string($value)
                || !$this->_hasInTranslateList($stringsList, $key)) {
                continue;
            }
            
            $options[$key] = icl_translate(
                $wpmlPluginName,
                $key.' ('.$optionName.')',
                $options[$key]
            );
        }
        
        return $options;
    } // end onGetWpmlStringsFilter
    
    public function onRegisterWpmlStringsFilter($options, $optionName)
    {
        if (!function_exists('icl_register_string')) {
            return false;
        }
        
        $wpmlPluginName = $this->getWpmlPrefix(); 
        $stringsList = $this->getTranslateList();
        
        if (!array_key_exists($optionName, $stringsList) ) {
            return false;
        }
        $stringsList = $stringsList[$optionName];
        
        if (is_string($options) && is_string($stringsList)) {
            icl_register_string(
                $wpmlPluginName,
                $stringsList,
                $options
            );
            
            return true;
        }
        
        foreach ($options as $key => $value) {
            if (is_string($value)
                && $this->_hasInTranslateList($stringsList, $key)) {
                icl_register_string(
                    $wpmlPluginName,
                    $key.' ('.$optionName.')',
                    $value
                );
            }
        }
    } // end onRegisterWpmlStringsFilter
    
    public function getWpmlPrefix()
    {
        return $this->config->getWpmlPrefix();
    } // end getWpmlPrefix
    
    public function getTranslateList()
    {
        return $this->config->getTranslateList();
    } // end getTranslateList
    
    private function _hasInTranslateList($stringsList, $ident)
    {
        return in_array($ident, $stringsList);
    } // end _hasInTranslateList
}