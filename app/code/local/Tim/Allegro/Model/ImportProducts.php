<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_ImportProducts extends Mage_Core_Model_Abstract
{
    /**
     * Import products from xml
     */
    public function run()
    {
        $files = $this->_getXmlFiles();
        if ($files !== false) {
            if (!empty($files)) {
                foreach ($files as $file) {
                    $fileName = basename($file);
                    $sku = '';
                    $xml = new SimpleXMLElement(file_get_contents($file));
                    $rootNode = $xml->DataArea->ItemMaster->ItemMasterHeader;
                    $itemId = $rootNode->ItemID;
                    foreach ($itemId as $item) {
                        if ($item['agencyRole'] == 'TIM article code') {
                            $sku = (string) $item->ID;
                        }
                    }

                    if (empty($sku)) {
                        Mage::log('File ' . $fileName . ' is broken - missing <ItemID agencyRole="TIM article code"> tag with SKU.', null, 'tim_import.log');
                        continue;
                    }

                    $categoryArray = $this->_createCategoryArray($rootNode, $fileName);
                    if ($categoryArray !== false) {
                        $this->_createCategoryTree($categoryArray);
                    } else {
                        continue;
                    }
                }
            } else {
                Mage::log('There are no files in folder.', null, 'tim_import.log');
            }
        } else {
            Mage::log('Folder for xml files does not exist.', null, 'tim_import.log');
        }
    }

    /**
     * Takes all xml files from folder
     * @return array|bool
     */
    protected function _getXmlFiles()
    {
        $xmlPath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'xml';
        if (is_dir($xmlPath)) {
            $files = glob($xmlPath . DS . '*.xml');
            return $files;
        } else {
            return false;
        }
    }

    /**
     * Creates array with categories and ids.
     * @param $rootNode
     * @param $fileName
     * @return array|bool
     */
    protected function _createCategoryArray($rootNode, $fileName)
    {
        foreach ($rootNode->Classification as $item) {
            if ($item['type'] == 'GrupaProduktow' && $item->Note['type'] == 'NazwaHierarchii') {
                $groupsIds = explode('/', $item->Codes->Code);
                $groupsNames =(array) $item->Note;
                array_shift($groupsNames);
                array_shift($groupsNames);
                array_shift($groupsIds);
                $groups = array_combine($groupsIds, $groupsNames);
                if ($groups !== false) {
                    return $groups;
                } else {
                    Mage::log('File ' . $fileName . ' is broken.', null, 'tim_import.log');
                }
            }
        }

        return false;
    }

    /**
     * Creates category tree, if category exist - skips it.
     * @param $categoryArray
     */
    protected function _createCategoryTree($categoryArray)
    {
        $rootCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', 'B24');

        foreach ($categoryArray as $id => $name) {
            $currentCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', $id);

            if ($currentCategory !== false) {
                $rootCategory = $currentCategory;
                continue;
            } else {
                Mage::getModel('catalog/category')
                    ->setName($name)
                    ->setTimCategoryId($id)
                    ->setDisplayMode('PRODUCTS')
                    ->setAttributeSetId(Mage::getModel('catalog/category')->getDefaultAttributeSetId())
                    ->setIsActive(1)
                    ->setIsAnchor(1)
                    ->setPath(implode('/',$rootCategory->getPathIds()))
                    ->setInitialSetupFlag(true)
                    ->save();
                $rootCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', $id);
            }
        }
    }
}