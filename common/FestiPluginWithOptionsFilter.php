<?php

abstract class FestiPluginWithOptionsFilter extends FestiPlugin
{
    public function getOptions($optionName)
    {
        $options = parent::getOptions($optionName);

        $options = apply_filters(
            'festi_plugin_get_options',
            $options,
            $this->_optionsPrefix.$optionName
        );

        return $options;
    } // end getOptions

    public function updateOptions($optionName, $values = array())
    {
        do_action(
            'festi_plugin_update_options',
            $values,
            $this->_optionsPrefix.$optionName
        );

        return parent::updateOptions($optionName, $values);
    } // end updateOptions

    public function getCache($optionName)
    {
        if ($this->_isQtranslatePluginActive()) {
            return false;
        }

        $fileName = $this->getCaheFileName($optionName);

        if (!$fileName) {
            return false;
        }

        $file = $this->getPluginCachePath($fileName);

        if (!file_exists($file)) {
            return false;
        }

        $content = include($file);

        return $content;
    } // end getCache

    protected function getCaheFileName($folderName)
    {
        $fileFolderPath = $this->_pluginCachePath.$folderName.'/';

        if (!file_exists($fileFolderPath)) {
            return false;
        }

        $fileName = $this->getCaheFileNameFromFolder($fileFolderPath);

        if (!$fileName) {
            return false;
        }

        return $folderName.'/'.$fileName;
    } // end getCaheFileName

    protected function getCaheFileNameFromFolder($folderPath)
    {
        $filesList = scandir($folderPath);

        $filesList = array_slice($filesList, 2);

        if (!$filesList) {
            return false;
        }

        $filename = str_replace('.php', '', $filesList[0]);

        return $filename;
    } // end getCaheFileFromFolder

    public function updateCacheFile($folderName, $values)
    {
        if (!$this->_fileSystem) {
            $this->_fileSystem = $this->getFileSystemInstance();
        }

        if (!$this->_fileSystem) {
            return false;
        }

        if (!$this->_fileSystem->is_writable($this->_pluginCachePath)) {
            return false;
        }

        $content = "<?php return '".$values."';";

        $fileFolderPath = $this->_pluginCachePath.$folderName.'/';

        if (!file_exists($fileFolderPath)) {
            $this->_fileSystem->mkdir($fileFolderPath, 0777);
        } else {
            $this->deleteAllFilesFromFolder($fileFolderPath);
        }

        $fileName = $folderName.'/'.time();

        $filePath = $this->getPluginCachePath($fileName);

        $this->_fileSystem->put_contents($filePath, $content, 0777);
    } //end updateCacheFile

    protected function deleteAllFilesFromFolder($folderPath)
    {
        $filesList = scandir($folderPath);

        $filesList = array_slice($filesList, 2);

        if (!$filesList) {
            return false;
        }

        foreach ($filesList as $item) {
            if (is_file($folderPath.$item)) {
                unlink($folderPath.$item);
            }
        }
    } // end deleteAllFilesFromFolder

    private function _isQtranslatePluginActive()
    {
        return $this->isPluginActive('qtranslate-x/qtranslate.php');
    } // end _isQtranslatePluginActive

}