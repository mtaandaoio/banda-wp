<?php

class WooCartBackendFestiPlugin extends WooCartFestiPlugin
{
    const ADMIN_PAGE_SLUG = 'festi-cart';

    const WOOCARTPRO_ERROR_CODE_PERMISSION = '403';

    protected $_menuOptions = array(
        'settings' => "Settings",
        'importExport' => "Import & Export",
        'help' => "Help",
    );

    protected $_iconRgbColor = array(0, 0, 0);

    protected $_defaultMenuOption = 'settings';

    protected $_fileSystem = '';

    public function onInit()
    {
        $this->oInitWpmlTraslator();

        $this->addActionListener('admin_menu', 'onAdminMenuAction');

        $this->addFilterListener(
            'plugin_action_links_' . $this->_pluginDirName . 'cart.php',
            'onPluginSettingsLinkFilter'
        );
    } // end onInit

    public function onPluginSettingsLinkFilter($links)
    {
        $url = admin_url('admin.php?page=' . self::ADMIN_PAGE_SLUG);

        $vars = array(
            'url' => $url,
            'title' => $this->getLang('Settings')
        );

        $links[] = $this->fetch('plugin_settings_link.phtml', $vars);

        return $links;
    }

    public function _onFileSystemInstanceAction()
    {
        $this->_fileSystem = $this->getFileSystemInstance();
    } // end _onFileSystemInstanceAction

    public function onInstall($refresh = false)
    {
        if (!$this->_fileSystem) {
            $this->_fileSystem = $this->getFileSystemInstance();
        }

        // if ($this->_hasPermissionToCreateCacheFolder()) {
            // //$this->_fileSystem->mkdir($this->_pluginCachePath, 0777);
        // }

        //$customCssPath = $this->getCustomCssPath();

        // if ($this->_hasPermissionToCreateCustomCssFolder($customCssPath)) {
            // //$this->_fileSystem->mkdir($customCssPath, 0777);
        // }

        // $this->installUserIcons('user/');
// 
        // $this->installUserIcons('user/on_hover/');
// 
        // if ($refresh) {
            // return true;
        // }

        // $this->_doInitDefaultSettings();
        // $widget = $this->onWidgetInitAction('instal');
        // $this->_doInitDefaultOptions('widget_options', $widget);
// 
        // $this->_updateCookieCacheFile();
    } // end onInstal

    private function _doInitDefaultSettings()
    {
        $this->_doInitDefaultOptions('settings');

        $file = $this->_pluginStaticPath . 'default_options/settings.txt';

        if (file_exists($file)) {
            $content = $this->_fileSystem->get_contents($file);
            $this->doImportSettingsFromJson($content);
        }
    } // end doInitDefaultOptionsSettings

    public function installUserIcons($folder)
    {
        $userIconsPath = $this->getPluginIconsPath($folder);

        if ($this->_hasPermissionToCreateUserIconsFolder($userIconsPath)) {
            $result = $this->_fileSystem->mkdir($userIconsPath, 0777);
        }

        if ($this->_fileSystem->is_writable($userIconsPath)) {
            try {
                $this->_doInitCopyDefaultIconsToUserDir($folder);
            } catch (Exception $e) {
                echo $e->getMessage();
                exit();
            }
        }
    } // end installUserIcons

    private function _hasPermissionToCreateCacheFolder()
    {
        return ($this->_fileSystem->is_writable($this->_pluginPath)
            && !file_exists($this->_pluginCachePath));

    } // end _hasPermissionToCreateFolder

    private function _hasPermissionToCreateCustomCssFolder($customCssPath)
    {
        $frontendCssPath = $file = $this->getPluginCssPath('frontend/');

        return ($this->_fileSystem->is_writable($frontendCssPath)
            && !file_exists($customCssPath));
    } // end _hasPermissionToCreateCustomCssFolder

    private function _hasPermissionToCreateUserIconsFolder($userIconsPath)
    {
        $iconsPath = $this->getPluginIconsPath();

        return ($this->_fileSystem->is_writable($iconsPath)
            && !$this->_fileSystem->exists($userIconsPath));

    } // end _hasPermissionToCreateUserIconsFolder

    public function onUninstall($refresh = false)
    {
        delete_option('festi_cart_settings');
        delete_option('festi_cart_widget_options');
        delete_option('festi_cart_coockie');
    } // end onUninstall

    private function _doInitCopyDefaultIconsToUserDir($folder)
    {
        $iconPath = $this->getPluginIconsPath('default/');

        $files = $this->_getListFilesInDirectory($iconPath);

        if (!$files) {
            $message = __(
                "The catalog is not detected files that come bundled " .
                "with the plugin.",
                $this->_languageDomain
            );
            $message .= PHP_EOL;
            $message .= __("Directory: ", $this->_languageDomain);
            $message .= $dirPath;

            throw new Exception($message);
        }

        $newIconPath = $this->getPluginIconsPath($folder);

        foreach ($files as $value) {
            $vars = array(
                'defaultIconPath' => $iconPath . $value,
                'userIconPath' => $newIconPath . $value

            );
            try {
                $this->doUpdateIconSize($vars);
            } catch (Exception $exp) {
                $this->_displayPermissionErrorForIcon($newIconPath . $value);
            }
        }
    } //end _doInitCopyDefaultIconsToUserDir

    public function doUpdateIconSize($vars, $colors = array(), $customType = '')
    {
        if ($vars) {
            extract($vars);
            if (!is_writable($userIconPath)) {
                throw new Exception(
                    "Undefined permission",
                    self::WOOCARTPRO_ERROR_CODE_PERMISSION
                );
            }
        }

        list($width, $height, $mime) = getimagesize($defaultIconPath);

        $imageType = $this->_getImageType($mime);

        $methodName = 'imagecreatefrom' . $imageType;

        $img = $methodName($defaultIconPath);

        if ($colors) {
            $img = $this->_changeColorOfIcons($img, $colors);
        }

        if ($this->_isUploadCustomIconWithIconSize($customType)) {
            $size = array(
                'width' => $_POST['customIconWidth'],
                'height' => $_POST['customIconHeight']
            );
        } else {
            $defaultIconSize = SettingsWooCartFacade::DEFAULT_ICON_SIZE;

            $size = array(
                'width' => $defaultIconSize,
                'height' => $defaultIconSize
            );

            $_POST['customIconWidth'] = $defaultIconSize;
            $_POST['customIconHeight'] = $defaultIconSize;
        }

        $newImage = $this->_doCreateNewImageWithUserSize($size);

        $vars = $size;
        $vars['originalWidth'] = $width;
        $vars['originalHeight'] = $height;
        $vars['originalImage'] = $img;
        $vars['newImage'] = $newImage;

        $newImage = $this->_doCopyOriginalImageToNewImage($vars);

        imagepng($newImage, $userIconPath);
    } //end doUpdateIconSize

    private function _displayPermissionErrorForIcon($userIconPath)
    {
        $message = __(
            "Available only standard icons! ",
            $this->_languageDomain
        );

        $message .= __(
            "You don't have permission to access: ",
            $this->_languageDomain
        );

        $message .= $userIconPath;

        $message .= $this->fetch('manual_url.phtml');

        $this->displayError($message);
    } //end _displayPermissionErrorForIcon

    private function _changeColorOfIcons($img, $colors)
    {
        if ($colors) {
            extract($colors);
        }

        $repaintIndexColor = imagecolorclosestalpha(
            $img,
            $fromRgb[0],
            $fromRgb[0],
            $fromRgb[0],
            0
        );

        imagecolorset(
            $img,
            $repaintIndexColor,
            $toRgb[0],
            $toRgb[1],
            $toRgb[2]
        );

        return $img;
    } // end _changeColorOfIcons

    private function _getImageType($mime)
    {
        $imageType = image_type_to_mime_type($mime);

        $imageType = str_replace('image/', '', $imageType);

        return $imageType;
    } // end _getImageType

    private function _isUploadCustomIconWithIconSize($customType)
    {
        return $this->isUploadCustomIcon($customType)
        && $this->_hasCustomIconSizeInRequest();
    } // end _isUploadCustomIconWithIconSize

    private function _hasCustomIconSizeInRequest()
    {
        return array_key_exists('customIconWidth', $_POST)
        && !empty($_POST['customIconWidth'])
        && array_key_exists('customIconHeight', $_POST)
        && !empty($_POST['customIconHeight']);
    } // end _hasCustomIconSizeInRequest

    public function isUploadCustomIcon($type)
    {
        return array_key_exists($type, $_FILES)
        && is_uploaded_file($_FILES[$type]['tmp_name']);
    } // end isUploadCustomIcon

    private function _doCreateNewImageWithUserSize($size = array())
    {
        if ($size) {
            extract($size);
        }

        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $image;
    } // end _doCreateNewImageWithUserSize

    private function _doCopyOriginalImageToNewImage($vars = array())
    {
        if ($vars) {
            extract($vars);
        }

        imagecopyresampled(
            $newImage,
            $originalImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $originalWidth,
            $originalHeight
        );

        return $newImage;
    } // end _doCopyOriginalImageToNewImage

    private function _getListFilesInDirectory($dirName)
    {
        if (!$this->_fileSystem->is_readable($dirName)) {
            $message = __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            $message .= $dirName;

            throw new Exception($message);
        }

        $files = $this->_fileSystem->dirlist($dirName);
        return array_keys($files);
    } // end _getListFilesInDirectory

    private function _updateCookieCacheFile()
    {
        $time = time();
        $content = md5($time);
        $content = array($content);
        $this->updateOptions('cookie', $content);
    } // end _updateCookieCacheFile

    public function getPluginTemplatePath($fileName)
    {
        return $this->_pluginTemplatePath . 'backend/' . $fileName;
    } // end getPluginTemplatePath

    public function getPluginCssUrl($fileName)
    {
        return $this->_pluginCssUrl . 'backend/' . $fileName;
    } // end getPluginCssUrl

    public function getPluginJsUrl($fileName)
    {
        return $this->_pluginJsUrl . 'backend/' . $fileName;
    } // end getPluginJsUrl

    public function onAdminMenuAction()
    {
        $page = add_menu_page(
            __('WooCart Pro Settings', $this->_languageDomain),
            __('Cart Options', $this->_languageDomain),
            'manage_options',
            self::ADMIN_PAGE_SLUG,
            array(&$this, 'onDisplayOptionPage'),
            $this->getPluginImagesUrl('icon_16x16.png')
        );

        $this->addActionListener(
            'admin_print_scripts-' . $page,
            'onInitJsAction'
        );

        $this->addActionListener(
            'admin_print_styles-' . $page,
            'onInitCssAction'
        );

        $this->addActionListener(
            'admin_head-' . $page,
            '_onFileSystemInstanceAction'
        );
    } // end onAdminMenuAction

    public function onInitJsAction()
    {
        $this->onEnqueueJsFileAction('jquery');
        $this->onEnqueueJsFileAction(
            'festi-cart-general',
            'general.js',
            'festi-cart-slider'
        );
        $this->onEnqueueJsFileAction(
            'festi-cart-colorpicker',
            'colorpicker.js',
            'jquery'
        );
        $this->onEnqueueJsFileAction(
            'festi-cart-tooltip',
            'tooltip.js',
            'festi-cart-colorpicker'
        );

        $this->onEnqueueJsFileAction(
            'festi-cart-slider',
            'slider.js',
            'festi-cart-tooltip'
        );

        $this->onEnqueueJsFileAction(
            'festi-cart-top-down-scroll-buttons',
            'top_down_scroll_buttons.js',
            'jquery'
        );

        $this->onEnqueueJsFileAction(
            'jquery-chosen',
            'vendor/chosen.jquery.min.js',
            'jquery'
        );

    } // end onInitJsAction

    public function onInitCssAction()
    {
        $this->onEnqueueCssFileAction(
            'festi-cart-styles',
            'style.css'
        );

        $this->onEnqueueCssFileAction(
            'festi-cart-colorpicker',
            'colorpicker.css'
        );

        $this->onEnqueueCssFileAction(
            'festi-cart-tooltip',
            'tooltip.css'
        );

        $this->onEnqueueCssFileAction(
            'festi-cart-slider',
            'slider.css'
        );

        $this->onEnqueueCssFileAction(
            'festi-cart-top-down-scroll-buttons',
            'top_down_scroll_buttons.css'
        );

        $this->onEnqueueCssFileAction(
            'jquery-chosen-styles',
            'vendor/chosen.css'
        );
    } // end onInitCssAction

    private function _doInitDefaultOptions($option, $instance = NULL)
    {
        // $methodName = $this->getMethodName('load', $option);
// 
        // if (is_null($instance)) {
            // $instance = $this;
        // }
// 
        // $method = array($instance, $methodName);
// 
        // if (!is_callable($method)) {
            // throw new Exception("Undefined method name: " . $methodName);
        // }
// 
        // $options = call_user_func_array($method, array());
        // foreach ($options as $ident => &$item) {
            // if ($this->_hasDefaultValueInItem($item)) {
                // $values[$ident] = $item['default'];
            // }
        // }
        // unset($item);

        //$this->updateOptions($option, $values);
    } // end _doInitDefaultOptions

    public function getMethodName($prefix, $option)
    {
        $option = explode('_', $option);

        $option = array_map('ucfirst', $option);

        $option = implode('', $option);

        $methodName = $prefix . $option;

        return $methodName;
    } // end getMethodName

    private function _hasDefaultValueInItem($item)
    {
        return isset($item['default']);
    } //end _hasDefaultValueInItem

    public function onDisplayOptionPage()
    {
        $this->addFilterListener(
            'admin_footer_text',
            'onAdminFooterReplaceFilter'
        );

        $menu = $this->fetch('menu.phtml');
        echo $menu;

        $methodName = 'fetchOptionPage';

        if ($this->hasOptionPageInRequest()) {
            $postfix = $_GET['tab'];
        } else {
            $postfix = $this->_defaultMenuOption;
        }
        $methodName .= ucfirst($postfix);

        $method = array(&$this, $methodName);

        if (!is_callable($method)) {
            throw new Exception("Undefined method name: " . $methodName);
        }

        call_user_func_array($method, array());
    } // end onDisplayOptionPage

    public function loadSettings()
    {
        $settingFacade = new SettingsWooCartFacade($this);

        return $settingFacade->getPreparedFields();
    }


    public function onAdminFooterReplaceFilter()
    {
        $vars = array(
            'created' => $this->getLang('Created by'),
            'support' => $this->getLang(
                'Premium Support and Custom Development Services'
            ),
            'url' => 'https://festi.team/'
        );

        echo $this->fetch('admin_footer.phtml', $vars);
    }

    public function fetchOptionPageSettings()
    {
        $vars = array();

        if ($this->_isRefreshPlugin()) {
            $this->onRefreshPlugin();

            $message = __(
                'Success update plugin',
                $this->_languageDomain
            );

            $this->displayUpdate($message);
        }

        $this->_displayFoldersAccessErrors();

        if ($this->_isDeleteCostumIcon()) {
            $this->onDeleteCustomIcon();

            $message = __(
                'Success custom icon remove',
                $this->_languageDomain
            );

            $this->displayUpdate($message);
        }

        if ($this->isUpdateOptions('save')) {
            try {
                $this->_doUpdateOptions($_POST);

                $message = __(
                    'Success update settings',
                    $this->_languageDomain
                );

                $this->displayUpdate($message);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }

        $options = $this->getSettings();

        $vars['fieldset'] = $this->getOptionsFieldSet();
        $vars['currentValues'] = $options;

        echo $this->fetch('settings_page.phtml', $vars);
    } // end fetchOptionPageSettings

    private function _isRefreshPlugin()
    {
        return array_key_exists('refresh_plugin', $_GET);
    } // end _isRefreshPlugin

    public function onRefreshPlugin()
    {
        //$this->onUninstall(true);
        $this->onInstall(true);
    } // end onRefreshPlugin

    private function _doUpdateOptions($newSettings = array())
    {
        if ($this->isUploadCustomIcon('customIcon')) {
            $this->_doUploadCustomIcon('customIcon');
            $newSettings = array_merge($newSettings, $_POST);
        }

        if ($this->isUploadCustomIcon('customIconOnHover')) {
            $this->_doUploadCustomIcon('customIconOnHover');
            $newSettings = array_merge($newSettings, $_POST);
        }

        $currentSettings = $this->getSettings();

        $this->updateColorOfDefaultIcons(
            'iconColor',
            $currentSettings,
            $newSettings
        );

        $this->updateColorOfDefaultIcons(
            'iconColorOnHover',
            $currentSettings,
            $newSettings
        );

        $this->_doDisableBadgeCount($newSettings);

        $settingsFacade = new SettingsWooCartFacade($this);
        $settingsFacade->sync($newSettings, $currentSettings);

        $this->_doCreateCustomCssFiles($newSettings);

        $this->_updateCookieCacheFile();
    } // end _doUpdateOptions

    private function _hasPermissionToCreateCustomCssFile()
    {
        $path = $this->getCustomCssPath();

        return $this->_fileSystem->exists($path)
        && $this->_fileSystem->is_writable($path);
    } // end _hasPermissionToCreateCustomCssFile

    private function _doCreateCustomCssFiles($options)
    {
        if (!$this->_hasPermissionToCreateCustomCssFile()) {
            return false;
        }

        $filesNamesList = $this->getFileNamesListOfCustomizeCart();

        $vars = array(
            'settings' => $options,
            'banda' => $this->getBandaInstance(),
        );

        foreach ($filesNamesList as $fileName) {
            $cssContent = $this->_fetchFrontendCustomizeTemplate(
                $fileName . '.phtml',
                $vars
            );

            $cssContent = str_replace('<style>', '', $cssContent);
            $cssContent = str_replace('</style>', '', $cssContent);

            $customCssFile = $this->getCustomCssPath($fileName . '.css');
            $this->_fileSystem->put_contents($customCssFile, $cssContent, 0777);
        }
    } // end _doCreateCustomCssFiles

    private function _fetchFrontendCustomizeTemplate($fileName, $vars)
    {
        return $this->fetch('../frontend/customize/' . $fileName, $vars);
    } // end _fetchFrontendCustomizeTemplate

    public function updateColorOfDefaultIcons($type, $options, $newSettings)
    {
        $iconsFolders = array(
            'iconColor' => 'user/',
            'iconColorOnHover' => 'user/on_hover/'
        );

        $currentIconColor = $options[$type];

        $colors['from'] = $currentIconColor;

        if ($this->_hasIconColorInRequest($newSettings)) {
            $colors['to'] = $newSettings[$type];
        }

        if (!$this->isIconColorChanged($colors)) {
            $this->updateIconsColor($colors['to'], $iconsFolders[$type]);
        }
    }  // end updateColorOfDefaultIcons

    public function updateIconsColor($color, $folder)
    {
        $styles = $this->_getSettingsFields();

        $iconList = $styles['iconList']['images'];

        $defaultIconsPath = $this->getPluginIconsPath('default/');

        if (!$this->_fileSystem->is_readable($defaultIconsPath)) {
            $message = __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            $message .= $defaultIconsPath;

            throw new Exception($message);
        }

        $userIconsPath = $this->getPluginIconsPath($folder);

        $colors = array(
            'fromRgb' => $this->_iconRgbColor,
            'toRgb' => $this->convertHexToRgb($color)
        );

        foreach ($iconList as $value) {
            $icons = array(
                'defaultIconPath' => $defaultIconsPath . $value,
                'userIconPath' => $userIconsPath . $value
            );
            try {
                $this->doUpdateIconSize($userIconsPath . $value);
            } catch (Exception $exp) {
                $this->_displayPermissionErrorForIcon($userIconsPath . $value);
            }
        }
    } // end updateIconsColor

    private function _hasIconColorInRequest($settings = array())
    {
        return array_key_exists('iconColor', $settings)
        && !empty($settings['iconColor']);
    } // end _hasIconColorInRequest

    public function isIconColorChanged($colors = array())
    {
        extract($colors);
        return $from == $to;
    } //end isIconColorChanged

    private function _displayFoldersAccessErrors()
    {
        $caheFolderErorrs = $this->_detectTheCacheFolderAccessErrors();

        $userFolderErorrs = $this->_detectTheUserIconsFolderAccessErrors();

        $customCssFolderErorrs = $this->_detectTheCustomCssFolderAccessErrors();

        if ($caheFolderErorrs || $userFolderErorrs || $customCssFolderErorrs) {
            echo $this->fetch('refresh.phtml');
        }
    } // end _displayFoldersAccessErrors

    private function _detectTheCacheFolderAccessErrors()
    {
        if (!$this->_fileSystem->is_writable($this->_pluginCachePath)) {

            $message = __(
                "Caching does not work! ",
                $this->_languageDomain
            );

            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );

            $path = $this->_pluginCachePath;

            if (!$this->_fileSystem->exists($path)) {
                $path = $this->_pluginPath;
            }

            $message .= $path;
            $message .= $this->fetch('manual_url.phtml');

            $this->displayError($message);

            return true;
        }

        return false;
    } // end _detectTheCacheFolderAccessErrors

    private function _detectTheUserIconsFolderAccessErrors()
    {
        $userIconsPath = $this->getPluginIconsPath('user/');
        if (!$this->_fileSystem->is_writable($userIconsPath)) {

            $this->_currentIconFolder = 'default';

            $message = __(
                "Available only standard icons! ",
                $this->_languageDomain
            );

            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );

            $path = $userIconsPath;

            if (!$this->_fileSystem->exists($userIconsPath)) {
                $path = $this->getPluginIconsPath();
            }

            $message .= $path;
            $message .= $this->fetch('manual_url.phtml');

            $this->displayError($message);

            return true;
        }

        return false;
    } // end _detectTheUserIconsFolderAccessErrors

    private function _detectTheCustomCssFolderAccessErrors()
    {
        $customCssPath = $this->getCustomCssPath();
        if (!$this->_fileSystem->is_writable($customCssPath)) {

            $message = __(
                "Styles for Customizing of cart will not added in css file! ",
                $this->_languageDomain
            );

            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );

            $path = $customCssPath;

            if (!$this->_fileSystem->exists($customCssPath)) {
                $path = $this->_pluginCssPath . 'frontend/';
            }

            $message .= $path;

            $this->displayError($message);

            return true;
        }

        return false;
    } // end _detectTheUserIconsFolderAccessErrors

    public function isUpdateOptions($action)
    {
        return array_key_exists('__action', $_POST)
        && $_POST['__action'] == $action;
    } // end isUpdateOptions

    public function getOptionsFieldSet()
    {
        $fildset = array(
            'general' => array(
                'legend' => __('General', $this->_languageDomain),
                'display' => true
            ),
            'menu' => array(
                'legend' => __('Menu', $this->_languageDomain)
            ),
            'window' => array(
                'legend' => __('Cart in Fixed Location', $this->_languageDomain)
            ),
            'cartCustomization' => array(
                'legend' => __(
                    'Customization for Cart Container',
                    $this->_languageDomain
                )
            ),
            'dropdownListCustomization' => array(
                'legend' => __(
                    'Customization for Product List',
                    $this->_languageDomain
                )
            ),
            'popup' => array(
                'legend' => __(
                    ' Lightbox Popup for Add to Cart action',
                    $this->_languageDomain
                )
            ),
        );

        $settings = $this->_getSettingsFields();

        if ($settings) {
            foreach ($settings as $ident => &$item) {
                if (array_key_exists('fieldsetKey', $item)) {
                    $key = $item['fieldsetKey'];
                    $fildset[$key]['filds'][$ident] = $settings[$ident];
                }
            }
            unset($item);
        }

        return $fildset;
    } // end getOptionsFieldSet

    private function _doUploadCustomIcon($type)
    {
        $iconsFolders = array(
            'customIcon' => 'user/',
            'customIconOnHover' => 'user/on_hover/'
        );

        if (!$this->_isAllowedCustomIconExtension($type)) {
            $message = __(
                "Wrong Image Format",
                $this->_languageDomain
            );

            throw new Exception($message);
        }

        $iconName = 'custom_icon.png';
        $iconPath = $this->getPluginIconsPath($iconsFolders[$type] . $iconName);

        $variables = array(
            'defaultIconPath' => $_FILES[$type]["tmp_name"],
            'userIconPath' => $iconPath
        );

        try {
            $this->doUpdateIconSize($variables, array(), $type);
        } catch (Exception $exp) {
            $this->_displayPermissionErrorForIcon($iconPath);
        }


        $_POST[$type] = $iconName;

        if ($type == 'customIcon') {
            $_POST['iconList'] = 0;
        }
    } // end _doUploadCustomIcon

    private function _isAllowedCustomIconExtension($type)
    {
        $ext = pathinfo($_FILES[$type]['name'], PATHINFO_EXTENSION);

        return in_array($ext, array('png', 'gif', 'jpg', 'jpeg'));
    } // end _isAllowedCustomIconExtension

    public function onDeleteCustomIcon()
    {
        $options = $this->getSettings();

        $options['customIcon'] = '';
        $options['customIconOnHover'] = '';

        $value = $options['iconList'];

        if ($this->_isWasMainIcon($value)) {
            $settings = $this->_getSettingsFields();
            $options['iconList'] = $settings['iconList']['default'];
        }

        $this->updateOptions('settings', $options);

        unset($_GET['delete_custom_icon']);

    } // end onDeleteCustomIconAction

    private function _isWasMainIcon($var)
    {
        return $var == 0;
    } // end _isWasMainIcon

    public function getSelectorClassForDisplayEvent($class)
    {
        $selector = $class . '-visible';

        $options = $this->getSettings();

        if (!isset($options[$class]) || $options[$class] == 'disable') {
            $selector .= ' festi-cart-hidden ';
        }

        return $selector;
    } // end getSelectorClassForDisplayEvent


    protected function hasOptionPageInRequest()
    {
        return array_key_exists('tab', $_GET)
        && array_key_exists($_GET['tab'], $this->_menuOptions);
    } // end hasOptionPageInRequest

    private function _isDeleteCostumIcon()
    {
        return array_key_exists('delete_custom_icon', $_GET);
    } // end _isDeleteCostumIcon

    public function fetchOptionPageHelp()
    {
        echo $this->fetch('help_page.phtml');
    } // end fetchOptionPageManual

    public function fetchOptionPageImportExport()
    {
        if ($this->isUpdateOptions('import')) {
            try {
                $this->doImportSettingsFromJson($_POST['importSettings']);

                $message = __(
                    'Success update settings',
                    $this->_languageDomain
                );

                $this->displayUpdate($message);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }

        $vars = array(
            'jsonCode' => $this->getJsonForExport()
        );

        echo $this->fetch('import_export_page.phtml', $vars);
    } // end fetchOptionPageImportExport

    public function getJsonForExport()
    {
        $options = $this->getOptionsWithDisabledValues();

        $options = json_encode($options);

        return $options;
    } // end getJsonForExport

    public function getOptionsWithDisabledValues()
    {
        $options = $this->getSettings();

        $settings = $this->_getSettingsFields();

        $diff = array_diff_key($settings, $options);

        $disabledOptions = array();

        foreach ($diff as $key => $value) {
            if ($this->_isSwitchOption($value)) {
                $disabledOptions[$key] = 'false';
            }
        }

        $options = array_merge($options, $disabledOptions);

        return $options;

    } // end getOptionsWithDisabledValues

    private function _isSwitchOption($value)
    {
        return $value['type'] == 'input_checkbox';
    } // end _isSwitchOption

    public function doImportSettingsFromJson($json = '')
    {
        if (!$json) {
            $message = __(
                'You need to insert JSON',
                $this->_languageDomain
            );
            throw new Exception($message);
        }

        $importSettings = stripcslashes($json);
        $importSettings = json_decode($importSettings, true);

        if (!is_array($importSettings)) {
            $message = __(
                'Not true format settings',
                $this->_languageDomain
            );
            throw new Exception($message);
        }

        $importSettings = $this->getOnlySupportedKeys($importSettings);

        $newOptions = $this->getNewOptions($importSettings);

        $this->_doUpdateOptions($newOptions);
    } // end doImportSettingsFromJson

    public function getOnlySupportedKeys($importSettings)
    {
        $settings = $this->_getSettingsFields();

        $diff = array_diff_key($importSettings, $settings);
        $importSettings = array_diff_key($importSettings, $diff);

        if (empty($importSettings)) {
            $message = __(
                'These settings are not supported',
                $this->_languageDomain
            );
            throw new Exception($message);
        }

        return $importSettings;

    } // end getOnlySupportedKeys

    public function getNewOptions($importSettings)
    {
        $options = $this->getSettings();

        $newOptions = array_merge($options, $importSettings);

        $newOptions = $this->deleteOptionsOfDisabledValue($newOptions);

        return $newOptions;
    } // end getNewOptions

    private function _getSettingsFields()
    {
        $settingsFacade = new SettingsWooCartFacade($this);

        return $settingsFacade->getPreparedFields();
    } // end _getSettingsFields

    public function deleteOptionsOfDisabledValue($options)
    {
        $diff = array_keys($options, 'false');

        $diff = array_fill_keys($diff, '');

        $options = array_diff_key($options, $diff);

        return $options;
    } // end deleteOptionsOfDisabledValue

    private function _isWorkingDefaultCount(&$settings)
    {
        return $settings['QuantityDisplayingType'] == 'defaultCount';
    } // end _isWorkingDefaultCount

    private function _doDisableBadgeCount(&$settings)
    {
        if ($this->_isWorkingDefaultCount($settings)) {
            $settings['LocationInCart'] = false;
        }
    } // end _doDisableBadgeCount
}