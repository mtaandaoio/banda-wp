<?php
require_once dirname(__FILE__).'/WooCartProTestCase.php';

class WooCartBackendFestiPluginTest extends WooCartProTestCase
{
    public function testGetSettings()
    {
        $backend = $this->getBackendInstance();
        $settings = $backend->getSettings();
        
        $this->assertTrue(!empty($settings));
    } // end testGetSettings

    /**
     * @ticket 2586
     * @expectedException Exception
     */
    public function testAccessPermissions()
    {
        $userIconPath = $this->getPluginPath(
            '/static/images/icons/user/test.png'
        );

        if (file_exists($userIconPath)) {
            unlink($userIconPath);
        }
        $file = fopen($userIconPath, 'x');
        chmod($userIconPath, 0444);

        $vars = array(
            'defaultIconPath' => $this->getPluginPath(
                '/static/images/icons/default/icon1.png'
            ),
            'userIconPath'    => $userIconPath
        );

        $this->getBackendInstance()->doUpdateIconSize($vars);

        fclose($file);
        unlink($userIconPath);
    } // end testAccessPermissions 

}