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
}