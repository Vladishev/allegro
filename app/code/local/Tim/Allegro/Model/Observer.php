<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_Observer
{
    /**
     * Deletes uploaded file(checkbox 'Delete' in System->Config->TIM SA->Import products from CSV)
     * @return $this
     */
    public function deleteUploadedFile()
    {
        $postData = Mage::app()->getRequest()->getParams();
        $deleteValues = $postData['groups']['tim_import_interface']['fields']['import']['value'];

        if ($deleteValues['delete']) {
            $fileName = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'csv' . DS . $deleteValues['value'];
            unlink($fileName);
        }
        return $this;
    }

    /**
     * Cleans folder of old files
     * @return $this
     */
    public function cleanImageDir()
    {
        $uploadedFile = Mage::app()->getStore()->getConfig('tim_alternative_image/tim_alternative_image_group/alternative_image');
        $path = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'alternative_image' . DS;
        $files = glob($path . '*');
        if (!empty($files[0])) {
            foreach ($files as $file) {
                if (basename($file) == $uploadedFile) {
                    continue;
                }
                unlink($file);
            }
        }
        $this->_resizeImage($path . $uploadedFile);

        return $this;
    }

    /**
     * Resize image after download
     * @param $file
     */
    protected function _resizeImage($file)
    {
        $imageInfo = getimagesize($file);
        try {
            $imageObj = new Varien_Image($file);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            if ($imageInfo[0] > 800) {
                $imageObj->resize(800, false);
            }
            $imageObj->save($file);
        } catch (Exception $e) {
            Mage::log('Can not resize alternative image. Technical details: ' . $e->getMessage(), null, 'tim_import.log');
        }
    }
}