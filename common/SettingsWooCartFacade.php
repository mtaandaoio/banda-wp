<?php

class SettingsWooCartFacade
{
    const PLUGIN_OPTION_KEY_NAME = 'settings';
    const DEFAULT_ICON_SIZE = 20;

    /**
     * @var IFestiEngine
     */
    private $_plugin;

    public function __construct(&$plugin)
    {
        $this->_plugin = $plugin;
    }

    public function getOptions()
    {
        $options = $this->_plugin->getOptions(static::PLUGIN_OPTION_KEY_NAME);
        if (!is_array($options)) {
            $options = array();
        }

        $fields = $this->_getFields();

        foreach ($fields as $key => $data) {
            if (array_key_exists($key, $options)) {
                continue;
            }

            if (array_key_exists('default', $data)) {
                $options[$key] = $data['default'];
            } else {
                $options[$key] = null;
            }
        }

        return $options;
    } // end getOptions

    public function sync($newSettings, $currentSettings = false)
    {
        if (!$currentSettings) {
            $currentSettings = $this->getOptions();
        }

        foreach ($currentSettings as $key => $value) {
            if (!array_key_exists($key, $newSettings)) {
                $newSettings[$key] = null;
            }
        }


        $this->_plugin->updateOptions(
            static::PLUGIN_OPTION_KEY_NAME,
            $newSettings
        );

        return true;
    } // end sync

    public function getPreparedFields()
    {
        $settings = $this->_getFields();

        // TODO: Move to WP Facade
        $menus = $this->_getListWordpressMenu();
        if ($menus) {
            foreach ($menus as $menu) {
                $settings['menuList']['values'][$menu->slug] = $menu->name;
            }
        }

        $pages = $this->_getPublishedPages();
        $pageList = $this->_getPagesList($pages);
        $settings['displayCartOnPage']['values'] = $pageList;

        $values = $this->_plugin->getOptions(static::PLUGIN_OPTION_KEY_NAME);

        if ($values) {
            foreach ($settings as $ident => &$item) {
                if (array_key_exists($ident, $values)) {
                    $item['value'] = $values[$ident];
                }
            }
            unset($item);
        }

        if (!empty($values['iconList'])) {
            $currentIconValue = $values['iconList'];

            if ($this->_isSelectedCustomIcon($currentIconValue)) {
                $settings['customIcon']['selected'] = 1;
            }
        }

        return $settings;
    } // end getPreparedFields

    private function _getPagesList($pages)
    {
        $pageList = array();
        foreach ($pages as $page) {
            $title = $page->post_title;
            $id = $page->ID;
            $pageList[$id] = $title;
        }

        return $pageList;
    } // end _getPagesList

    private function _getPublishedPages()
    {
        $params = array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'posts_per_page' => -1
        );

        $pages = get_posts($params);

        return $pages;
    } // end _getPublishedPages

    private function _getListWordpressMenu()
    {
        return get_terms('nav_menu', array('hide_empty' => false));
    } //end _getListWordpressMenu

    private function _isSelectedCustomIcon($value)
    {
        return $value == 0;
    } // end _isSelectedCustomIcon

    /**
     * Returns all exists options for cart.
     *
     * @return array
     */
    private function _getFields()
    {
        $settings = array(
            'displayMenu' => array(
                'caption' => $this->_plugin->getLang(
                    'Cart in Menu'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable displaying cart in menu'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'menu',
                'backlight' => 'light'
            ),

            'menuList' => array(
                'caption' => $this->_plugin->getLang(
                    'Displaying in Menu'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select where you want to show cart '
                ),
                'type' => 'select',
                'attr' => 'multiple',
                'default' => array(),
                'eventClasses' => 'displayMenu',
                'fieldsetKey' => 'menu'
            ),

            'menuCartPosition' => array(
                'caption' => $this->_plugin->getLang(
                    'Position in Menu'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the location of cart in the menu'
                ),
                'type' => 'select',
                'values' => array(
                    0 => $this->_plugin->getLang(
                        'Left'
                    ),
                    1 => $this->_plugin->getLang(
                        'Right'
                    )
                ),
                'default' => 1,
                'eventClasses' => 'displayMenu',
                'fieldsetKey' => 'menu'
            ),

            'customizeCartInMenu' => array(
                'caption' => $this->_plugin->getLang(
                    'Enable Customization for Cart In Menu'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'eventClasses' => 'displayMenu',
                'fieldsetKey' => 'menu'
            ),

            'hideCart' => array(
                'caption' => $this->_plugin->getLang(
                    'Hide Empty Cart'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable hide cart option'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'fieldsetKey' => 'general'
            ),

            'displayCartOnAllPages' => array(
                'caption' => $this->_plugin->getLang(
                    'Display Cart on All Pages'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable display cart on all pages option'
                ),
                'type' => 'input_checkbox',
                'event' => 'invisible',
                'default' => 1,
                'fieldsetKey' => 'general',
                'hint' => $this->_plugin->getLang(
                    'Uncheck box to select certain pages where' .
                    ' you want shopping cart to be displayed on'
                )
            ),

            'displayCartOnPage' => array(
                'caption' => $this->_plugin->getLang(
                    'Display Cart on Page:'
                ),
                'type' => 'multiple_select',
                'default' => array(),
                'fieldsetKey' => 'general',
                'eventClasses' => 'displayCartOnAllPages'
            ),

            'cartIconDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Icon'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'general'
            ),

            'displayIcon' => array(
                'caption' => $this->_plugin->getLang(
                    'Cart Icon'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable cart icon'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),

            'iconPosition' => array(
                'caption' => $this->_plugin->getLang(
                    'Icon Position'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change Icon position'
                ),
                'values' => array(
                    0 => $this->_plugin->getLang(
                        'Left'
                    ),
                    1 => $this->_plugin->getLang(
                        'Right'
                    )
                ),
                'type' => 'select',
                'default' => 0,
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),

            'iconList' => array(
                'caption' => $this->_plugin->getLang(
                    'Icons'
                ),
                'type' => 'icon_list',
                'images' => array(
                    1 => 'icon1.png',
                    2 => 'icon2.png',
                    3 => 'icon3.png',
                    4 => 'icon4.png',
                    5 => 'icon5.png',
                    6 => 'icon6.png',
                    7 => 'icon7.png',
                    8 => 'icon8.png',
                    9 => 'icon9.png',
                    10 => 'icon10.png',
                    11 => 'icon11.png',
                ),
                'default' => 5,
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),

            'iconColor' => array(
                'type' => 'skip',
                'default' => '#000000'
            ),

            'iconColorOnHover' => array(
                'caption' => $this->_plugin->getLang(
                    'Icon Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'hide' => 'ifDefaultFolder',
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'general',
                'eventClasses' => 'displayIcon'
            ),
            'customIconWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width for Custom Icon'
                ),
                'lable' => 'px',
                'type' => 'input_size',
                'default' => static::DEFAULT_ICON_SIZE,
                'eventClasses' => 'displayIcon',
                'class' => 'festi-cart-custom-icon-size',
                'fieldsetKey' => 'general'
            ),

            'customIconHeight' => array(
                'caption' => $this->_plugin->getLang(
                    'Height for Custom Icon'
                ),
                'lable' => 'px',
                'type' => 'input_size',
                'default' => static::DEFAULT_ICON_SIZE,
                'eventClasses' => 'displayIcon',
                'class' => 'festi-cart-custom-icon-size',
                'fieldsetKey' => 'general'
            ),
            'customIcon' => array(
                'caption' => $this->_plugin->getLang(
                    'Custom Icon'
                ),
                'hint' => $this->_plugin->getLang(
                    'Upload your own cart image'
                ),
                'type' => 'custom_icon',
                'default' => '',
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),
            'customIconOnHover' => array(
                'caption' => $this->_plugin->getLang(
                    'Custom Icon on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Upload your own cart image'
                ),
                'type' => 'custom_icon_on_hover',
                'default' => '',
                'eventClasses' => 'displayIcon',
                'fieldsetKey' => 'general'
            ),
            'cartDropdownListDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Dropdown List'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'general'
            ),
            'dropdownAction' => array(
                'caption' => $this->_plugin->getLang(
                    'Dropdown Product List'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select action to show dropdown product list'
                ),
                'values' => array(
                    'disable' => $this->_plugin->getLang(
                        'Disable'
                    ),
                    'hover' => $this->_plugin->getLang(
                        'On Hover'
                    ),
                    'click' => $this->_plugin->getLang(
                        'On Click'
                    ),
                ),
                'type' => 'select',
                'default' => 'click',
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
            'cartDropdownListAligment' => array(
                'caption' => $this->_plugin->getLang(
                    'Alignment Relative to Cart'
                ),
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 'left',
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'general',
            ),
            'dropdownArrow' => array(
                'caption' => $this->_plugin->getLang(
                    'Dropdown Arrow'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select for Show dropdown arrow'
                ),
                'values' => array(
                    0 => $this->_plugin->getLang(
                        'Hide'
                    ),
                    1 => $this->_plugin->getLang(
                        'Left'
                    ),
                    2 => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 1,
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'general'
            ),
            'dropdownListAmountProducts' => array(
                'caption' => $this->_plugin->getLang(
                    'Set Maximum Number of Products'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'general'
            ),
            'productListScroll' => array(
                'caption' => $this->_plugin->getLang(
                    'Scrollbar'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable scrollbar for products list'
                ),
                'type' => 'input_checkbox',
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
            'productListScrollHeight' => array(
                'caption' => $this->_plugin->getLang(
                    'Height'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 200,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 100,
                'max' => 1000,
                'fieldsetKey' => 'general',
                'eventClasses' => 'productListScroll',
            ),
            'cartQuantityDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Product Quantity'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'general'
            ),
            'QuantityDisplayingType' => array(
                'caption' => $this->_plugin->getLang(
                    'Quantity Displaying Type'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select how to display product quantity in cart'
                ),
                'values' => array(
                    'defaultCount' => $this->_plugin->getLang(
                        'Default Count'
                    ),
                    'badgeCount' => $this->_plugin->getLang(
                        'Badge Count'
                    )
                ),
                'type' => 'selecter',
                'default' => '',
                'fieldsetKey' => 'general'
            ),
            'LocationInCart' => array(
                'caption' => $this->_plugin->getLang(
                    'Location in Cart'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the alignment of badge in cart'
                ),
                'values' => array(
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'center' => $this->_plugin->getLang(
                        'Center'
                    )
                ),
                'type' => 'select',
                'default' => '',
                'fieldsetKey' => 'general'
            ),
            'displayCartQuantity' => array(
                'caption' => $this->_plugin->getLang(
                    'Product Quantity'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable total products amount in cart'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
            'displayQuantitySpinner' => array(
                'caption' => $this->_plugin->getLang(
                    'Quantity Spinner'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable quantity spinner in products list'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
            'textBeforeQuantity' => array(
                'caption' => $this->_plugin->getLang(
                    'Text Before Quantity'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change Text Before Quantity'
                ),
                'type' => 'input_double_text',
                'default' => '',
                'fieldsetKey' => 'general',
            ),
            'textBeforeQuantityPlural' => array(
                'type' => 'skip',
                'default' => '',
            ),

            'textAfterQuantity' => array(
                'caption' => $this->_plugin->getLang(
                    'Text After Quantity'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change Text After Quantity'
                ),
                'type' => 'input_double_text',
                'default' => 'Item',
                'fieldsetKey' => 'general',
            ),
            'textAfterQuantityPlural' => array(
                'type' => 'skip',
                'default' => 'Items',
            ),
            'cartTotalDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Total Price'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'general'
            ),
            'displayCartTotal' => array(
                'caption' => $this->_plugin->getLang(
                    'Cart Total Price'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable cart total price'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'general',
                'backlight' => 'light'
            ),
            'textBeforeTotal' => array(
                'caption' => $this->_plugin->getLang(
                    'Text Before Total Price'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change Text Before Total'
                ),
                'type' => 'input_text',
                'default' => '',
                'fieldsetKey' => 'general',
            ),

            'textAfterTotal' => array(
                'caption' => $this->_plugin->getLang(
                    'Text After Total Price'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change Text After Total'
                ),
                'type' => 'input_text',
                'default' => '',
                'fieldsetKey' => 'general',
            ),
            'windowCart' => array(
                'caption' => $this->_plugin->getLang(
                    'Show Cart'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable displaying cart in browser window'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'window',
                'backlight' => 'light'
            ),

            'windowCartHorizontalPosition' => array(
                'caption' => $this->_plugin->getLang(
                    'Horizontal Location in Window'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the horizontal location of cart in the window'
                ),
                'type' => 'select',
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'center' => $this->_plugin->getLang(
                        'Center'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    )
                ),
                'default' => 'right',
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),

            'windowCartVerticalPosition' => array(
                'caption' => $this->_plugin->getLang(
                    'Vertical Location in Window'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the vertical location of cart in the window'
                ),
                'type' => 'select',
                'values' => array(
                    'top' => $this->_plugin->getLang(
                        'Top'
                    ),
                    'middle' => $this->_plugin->getLang(
                        'Middle'
                    )
                ),
                'default' => 'top',
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),

            'windowCartMarginTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Top for Cart'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 50,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),

            'windowCartMarginLeft' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Left for Cart'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),

            'windowCartMarginRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Right for Cart'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 50,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window'
            ),
            'windowCartFixedPosition' => array(
                'caption' => $this->_plugin->getLang(
                    'Scrolling Cart'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable scrolling for cart'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'eventClasses' => 'windowCart',
                'fieldsetKey' => 'window',
            ),
            'popup' => array(
                'caption' => $this->_plugin->getLang(
                    'Show'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable displaying popup after adding product'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'popup',
                'backlight' => 'light'
            ),
            'popupWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 400,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 100,
                'max' => 1000,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupPadding' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackAroundDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Blacked out background'
                ),
                'type' => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupAroundBackColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupAroundBackOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackgroundDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Window'
                ),
                'type' => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackgroundColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBackgroundOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Shadow'
                ),
                'type' => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the shadow color'
                ),
                'type' => 'color_picker',
                'default' => '#5e5e5e',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 500,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupShadowBlur' => array(
                'caption' => $this->_plugin->getLang(
                    'Blur'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 1000,
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBorderDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Border'
                ),
                'type' => 'divider',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup'
            ),
            'popupBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 3,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupBorderRadius' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Radius'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#00a8ca',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupProductsListDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Products List'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupProductsListScroll' => array(
                'caption' => $this->_plugin->getLang(
                    'Scrollbar'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable scrollbar for products list'
                ),
                'type' => 'input_checkbox',
                'event' => 'visible',
                'eventClasses' => 'popup',
                'fieldsetKey' => 'popup',
                'backlight' => 'light'
            ),
            'popupProductsListScrollHeight' => array(
                'caption' => $this->_plugin->getLang(
                    'Height'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 200,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 100,
                'max' => 1000,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup'
            ),
            'popupHeaderTextDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Header Text Font Styles'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderText' => array(
                'caption' => $this->_plugin->getLang(
                    'Text'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the text'
                ),
                'type' => 'input_text',
                'default' => $this->_plugin->getLang(
                    'Item Added to your Cart!'
                ),
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextAlign' => array(
                'caption' => $this->_plugin->getLang(
                    'Alignment'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the location of text in popup header'
                ),
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'center' => $this->_plugin->getLang(
                        'Center'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 'center',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup'
            ),
            'popupHeaderTextFontSize' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Size'
                ),
                'lable' => 'px',
                'hint' => $this->_plugin->getLang(
                    'Change font size'
                ),
                'type' => 'input_size',
                'default' => 20,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#5b9e2b',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Top'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginBottom' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Bottom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 20,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginLeft' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Left'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupHeaderTextMarginRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Right'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Close button'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'displayPopupCloseButton' => array(
                'caption' => $this->_plugin->getLang(
                    'Display'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable close button'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
                'backlight' => 'light'
            ),
            'popupCloseButtonSize' => array(
                'caption' => $this->_plugin->getLang(
                    'Size'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 30,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 5,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#00a8ca',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonHoverColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#72ddf2',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonMarginTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Top'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupCloseButtonMarginRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Margin Right'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Continue Shopping Button'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'displayPopupContinueButton' => array(
                'caption' => $this->_plugin->getLang(
                    'Continue Shopping Button'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable Continue Shopping button'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
                'backlight' => 'light'
            ),
            'popupContinueButtonText' => array(
                'caption' => $this->_plugin->getLang(
                    'Title'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the button text'
                ),
                'type' => 'input_text',
                'default' => $this->_plugin->getLang(
                    'Continue Shopping'
                ),
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonFontSize' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Size'
                ),
                'lable' => 'px',
                'hint' => $this->_plugin->getLang(
                    'Change font size'
                ),
                'type' => 'input_size',
                'default' => 20,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonAlign' => array(
                'caption' => $this->_plugin->getLang(
                    'Alignment'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the location of button in popup footer'
                ),
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'center' => $this->_plugin->getLang(
                        'Center'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 'center',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonWidthType' => array(
                'caption' => $this->_plugin->getLang(
                    'Width Type'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select width type'
                ),
                'values' => array(
                    'auto' => $this->_plugin->getLang(
                        'Auto'
                    ),
                    'full' => $this->_plugin->getLang(
                        'Full Width'
                    ),
                    'custom' => $this->_plugin->getLang(
                        'Custom'
                    ),
                ),
                'type' => 'select',
                'default' => 'auto',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Custom Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 50,
                'max' => 1000,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonPaddingTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Top'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonPaddingBottom' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Bottom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonHoverBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#00a8ca',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonHoverFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#72ddf2',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBorderRadius' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Radius'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'popupContinueButtonHoverBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'popup',
                'eventClasses' => 'popup',
            ),
            'responsiveCartWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Disable Responsive Width'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'cartCustomization',
                'backlight' => 'light'
            ),
            'cartWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 20,
                'max' => 1000,
                'eventClasses' => 'responsiveCartWidth',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartPadding' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartContentAlign' => array(
                'caption' => $this->_plugin->getLang(
                    'Content Alignment'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the location of content in cart container'
                ),
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'center' => $this->_plugin->getLang(
                        'Center'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 'left',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity for Cart'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                //'fieldsetKey' => 'cartCustomization'
            ),
            'cartHoverOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Cart Opacity for Hover Action'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                //'fieldsetKey' => 'cartCustomization'
            ),
            'cartFontDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Styles'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'cartCustomization'
            ),
            'fontSize' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Size'
                ),
                'lable' => 'px',
                'hint' => $this->_plugin->getLang(
                    'Change font size'
                ),
                'type' => 'input_size',
                'default' => 14,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'cartCustomization'
            ),
            'textColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'cartCustomization'
            ),
            'textHoverColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBackgroundDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Background'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartHoverBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBackgroundOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 6,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartHoverBackgroundOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity on Hover'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 8,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Border'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#a39da3',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderHoverColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#b3afb3',
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusTopLeft' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Top Left Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusTopRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Top Right Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusBottomRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Bottom Right Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'cartBorderRadiusBottomLeft' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Bottom Right Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'cartCustomization'
            ),
            'productListPadding' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'responsiveProductList' => array(
                'caption' => $this->_plugin->getLang(
                    'Disable Responsive Width'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productListWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 170,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 150,
                'max' => 1000,
                'eventClasses' => 'responsiveProductList',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListFontSize' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Size'
                ),
                'lable' => 'px',
                'hint' => $this->_plugin->getLang(
                    'Change font size'
                ),
                'type' => 'input_size',
                'default' => 13,
                'class' => 'festi-cart-font-size',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyCartDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Empty Cart'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyText' => array(
                'caption' => $this->_plugin->getLang(
                    'Text for Empty Cart'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change Dropdown List Text for Empty Cart'
                ),
                'type' => 'input_text',
                'default' => 'There are no products',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'productListEmptyPaddingTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Top'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyPaddingBottom' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Bottom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListEmptyFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Text Font Color for Empty Cart'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color for empty product list text'
                ),
                'type' => 'color_picker',
                'default' => '#111111',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBackgroundDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Background'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBackgroundOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Border'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'borderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'type' => 'color_picker',
                'default' => '#ccc7c3',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'borderArrow' => array(
                'caption' => $this->_plugin->getLang(
                    'Show arrow on top of border'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable dropdown arrow'
                ),
                'type' => 'input_checkbox',
                //'default' => 1,
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'borderArrowColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Arrow Color'
                ),
                'type' => 'color_picker',
                'default' => '#ccc7c3',
                'eventClasses' => 'dropdownAction',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusTopLeft' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Top Left Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusTopRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Top Right Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusBottomRight' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Bottom Right Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListBorderRadiusBottomLeft' => array(
                'caption' => $this->_plugin->getLang(
                    'Radius for Bottom Left Corner'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 2,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListOpacity' => array(
                'caption' => $this->_plugin->getLang(
                    'Opacity for Product List'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 10,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 10,
                //'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productTitleDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Product Title'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductTitle' => array(
                'caption' => $this->_plugin->getLang(
                    'Display'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable product title'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#00497d',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductTitle'
            ),
            'productHoverFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#8094ed',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductTitle'
            ),
            'productAmountDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Amount and Price'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductTotalPrice' => array(
                'caption' => $this->_plugin->getLang(
                    'Display'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable amount and price'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productTotalPriceFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#1f1e1e',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductTotalPrice'
            ),
            'productListSubtotalDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Subtotal'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductListTotal' => array(
                'caption' => $this->_plugin->getLang(
                    'Display'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable Subtotal'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productListTotalText' => array(
                'caption' => $this->_plugin->getLang(
                    'Title'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the text'
                ),
                'type' => 'input_text',
                'default' => $this->_plugin->getLang(
                    'Subtotal'
                ),
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalTextAlign' => array(
                'caption' => $this->_plugin->getLang(
                    'Text Position'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the location of text in subtotal container'
                ),
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'center' => $this->_plugin->getLang(
                        'Center'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 'right',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#eeeeee',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color for total price in product list'
                ),
                'type' => 'color_picker',
                'default' => '#e6e6e6',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceBorderRadius' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Radius'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 7,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListTotalPriceFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductListTotal'
            ),
            'productListButtonsDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'View Cart & Checkout Buttons'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productListButtonsFontWeight' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Weight'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select font weight for Buttons'
                ),
                'values' => array(
                    'normal' => $this->_plugin->getLang(
                        'Normal'
                    ),
                    'bold' => $this->_plugin->getLang(
                        'Bold'
                    ),
                ),
                'type' => 'select',
                'default' => 'normal',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'productListButtonsQueue' => array(
                'caption' => $this->_plugin->getLang(
                    'Display the First in Queue'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select Display the button to display the first'
                ),
                'values' => array(
                    'viewCart' => $this->_plugin->getLang(
                        'View Cart'
                    ),
                    'checkout' => $this->_plugin->getLang(
                        'Checkout'
                    ),
                ),
                'type' => 'select',
                'default' => 'viewCart',
                'fieldsetKey' => 'dropdownListCustomization',
            ),
            'displayViewCartButton' => array(
                'caption' => $this->_plugin->getLang(
                    'View Cart Button'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable View Cart button'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'viewCartButtonText' => array(
                'caption' => $this->_plugin->getLang(
                    'Title'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the button text'
                ),
                'type' => 'input_text',
                'default' => $this->_plugin->getLang(
                    'View Cart'
                ),
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton'
            ),
            'viewCartButtonWidthType' => array(
                'caption' => $this->_plugin->getLang(
                    'Width Type'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select width type'
                ),
                'values' => array(
                    'auto' => $this->_plugin->getLang(
                        'Auto'
                    ),
                    'full' => $this->_plugin->getLang(
                        'Full Width'
                    ),
                    'custom' => $this->_plugin->getLang(
                        'Custom'
                    ),
                ),
                'type' => 'select',
                'default' => 'auto',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton'
            ),
            'viewCartButtonWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Custom Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 50,
                'max' => 1000,
                'eventClasses' => 'displayViewCartButton',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'viewCartButtonPaddingTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Top'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'viewCartButtonPaddingBottom' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Bottom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'viewCartButtonBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#eeeeee',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonHoverBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#6caff7',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonHoverFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonBorderRadius' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Radius'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 7,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'viewCartButtonHoverBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayViewCartButton',
            ),
            'displayCheckoutButton' => array(
                'caption' => $this->_plugin->getLang(
                    'Checkout Button'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable Checkout button'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'checkoutButtonText' => array(
                'caption' => $this->_plugin->getLang(
                    'Title'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the button text'
                ),
                'type' => 'input_text',
                'default' => $this->_plugin->getLang(
                    'Checkout'
                ),
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton'
            ),
            'checkoutButtonWidthType' => array(
                'caption' => $this->_plugin->getLang(
                    'Width Type'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select width type'
                ),
                'values' => array(
                    'auto' => $this->_plugin->getLang(
                        'Auto'
                    ),
                    'full' => $this->_plugin->getLang(
                        'Full Width'
                    ),
                    'custom' => $this->_plugin->getLang(
                        'Custom'
                    ),
                ),
                'type' => 'select',
                'default' => 'auto',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton'
            ),
            'checkoutButtonWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Custom Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 160,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 50,
                'max' => 1000,
                'eventClasses' => 'displayCheckoutButton',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'checkoutButtonPaddingTop' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Top'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'checkoutButtonPaddingBottom' => array(
                'caption' => $this->_plugin->getLang(
                    'Padding Bottom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 5,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'checkoutButtonBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#eeeeee',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonHoverBackground' => array(
                'caption' => $this->_plugin->getLang(
                    'Background Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the background color'
                ),
                'type' => 'color_picker',
                'default' => '#6caff7',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonHoverFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#ffffff',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonBorderWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Width'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonBorderRadius' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Radius'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 7,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 100,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'checkoutButtonHoverBorderColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Border Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e0e0e0',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayCheckoutButton',
            ),
            'productListDeleteButtonDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Delete Product Button'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayDeleteButton' => array(
                'caption' => $this->_plugin->getLang(
                    'Display'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable Delete Product Button'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'deleteButtonPosition' => array(
                'caption' => $this->_plugin->getLang(
                    'Position'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the location of button in dropdown list'
                ),
                'values' => array(
                    'left' => $this->_plugin->getLang(
                        'Left'
                    ),
                    'right' => $this->_plugin->getLang(
                        'Right'
                    ),
                ),
                'type' => 'select',
                'default' => 'left',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonVerticalAlignment' => array(
                'caption' => $this->_plugin->getLang(
                    'Vertical Alignment'
                ),
                'hint' => $this->_plugin->getLang(
                    'Select the vertical alignment of button in dropdown list'
                ),
                'values' => array(
                    'top' => $this->_plugin->getLang(
                        'Top'
                    ),
                    'middle' => $this->_plugin->getLang(
                        'Middle'
                    ),
                    'bottom' => $this->_plugin->getLang(
                        'Bottom'
                    ),
                ),
                'type' => 'select',
                'default' => 'top',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonSize' => array(
                'caption' => $this->_plugin->getLang(
                    'Size'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 18,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 5,
                'max' => 50,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the color'
                ),
                'type' => 'color_picker',
                'default' => '#000000',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'deleteButtonHoverFontColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Font Color on Hover'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#807878',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayDeleteButton'
            ),
            'productListDelimiterDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Divider for products'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'delimiterPositionsWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Height'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 1,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 15,
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'delimiterPositionsColor' => array(
                'caption' => $this->_plugin->getLang(
                    'Color'
                ),
                'hint' => $this->_plugin->getLang(
                    'Change the Color'
                ),
                'type' => 'color_picker',
                'default' => '#e8e4e3',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'productsPicturesDivider' => array(
                'caption' => $this->_plugin->getLang(
                    'Products Thumbnails'
                ),
                'type' => 'divider',
                'fieldsetKey' => 'dropdownListCustomization'
            ),
            'displayProductsPictures' => array(
                'caption' => $this->_plugin->getLang(
                    'Display'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable product picture'
                ),
                'type' => 'input_checkbox',
                'default' => 1,
                'event' => 'visible',
                'fieldsetKey' => 'dropdownListCustomization',
                'backlight' => 'light'
            ),
            'productDefaultThumbnail' => array(
                'caption' => $this->_plugin->getLang(
                    'Use Default Thumbnails'
                ),
                'lable' => $this->_plugin->getLang(
                    'Enable option'
                ),
                'hint' => $this->_plugin->getLang(
                    'Will use default  Banda Product Thumbnails'
                ),
                'type' => 'input_checkbox',
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductsPictures'
            ),
            'productImageMaxWidth' => array(
                'caption' => $this->_plugin->getLang(
                    'Max Width for Custom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 40,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 500,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductsPictures'
            ),
            'productImageMaxHeight' => array(
                'caption' => $this->_plugin->getLang(
                    'Max Height for Custom'
                ),
                'lable' => 'px',
                'type' => 'slider',
                'default' => 0,
                'class' => 'festi-cart-change-slider',
                'event' => 'change-slider',
                'min' => 0,
                'max' => 500,
                'fieldsetKey' => 'dropdownListCustomization',
                'eventClasses' => 'displayProductsPictures'
            ),
        );

        return $settings;
    }
}
