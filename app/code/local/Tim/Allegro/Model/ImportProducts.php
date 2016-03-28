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
                    $attributes = array();
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
                        $categoryId = $this->_createCategoryTree($categoryArray);
                        $attributes = $this->_getAttributes($rootNode);
//                        var_dump($productCategoryTimId);die;
                        $this->_createProduct($attributes, $categoryId);
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
     * @return int|string
     */
    protected function _createCategoryTree($categoryArray)
    {
        $productCategoryTimId = '';
        $rootCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', 'B24');

        foreach ($categoryArray as $id => $name) {
            $currentCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', $id);

            if ($currentCategory !== false) {
                $rootCategory = $currentCategory;
                $productCategoryTimId = $id;
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
                $productCategoryTimId = $id;
            }
        }
        $categoryId = Mage::getModel('catalog/category')
            ->loadByAttribute('tim_category_id', $productCategoryTimId)
            ->getId();

        return $categoryId;
    }

    protected function _createProduct($attributes, $categoryId)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        try {
            Mage::getModel('catalog/product')
                ->setWebsiteIds(array(0)) //website ID the product is assigned to, as an array
                ->setAttributeSetId(4) //ID of a attribute set named 'default'
                ->setTypeId('simple') //product type
                ->setCreatedAt(strtotime('now')) //product creation time
                ->setSku($attributes['sku']) //SKU
                ->setName($attributes['name']) //product name
                ->setWeight($attributes['weight'])
                ->setStatus(1) //product status (1 - enabled, 2 - disabled)
                ->setTaxClassId(1) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
                ->setPrice($attributes['price']) //price in form 11.22
                ->setDescription($attributes['description'])
                ->setShortDescription($attributes['description'])
                ->setTimCzyMagazynowy($attributes['tim_czy_magazynowy'])
                ->setTimJednostkaLogistyczna($attributes['tim_jednostka_logistyczna'])
                ->setTimJednostkaMiary($attributes['tim_jednostka_miary'])
                ->setTimProducent($attributes['tim_producent'])
                ->setTimWolumen($attributes['tim_wolumen'])
                ->setTimNrKatalogowyProducenta($attributes['tim_nr_katalogowy_producenta'])
//                ->setMediaGallery(array('images'=>array (), 'values'=>array ())) //media gallery initialization
//                ->addImageToMediaGallery('media/catalog/product/1/0/10243-1.png', array('image','thumbnail','small_image'), false, false)
                ->setCategoryIds(array($categoryId)) //assign product to categories
                ->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage()/*'Can not create product from file ' . $attributes['sku'] . '.xml.'*/, null, 'tim_import.log');
        }
    }

    protected function _getAttributes($rootNode)
    {
        $attributes = array();
        //getting tim_czy_magazynowy attribute
        foreach ($rootNode->Classification as $classification) {
            if ($classification['type'] == 'KategoriaDostawcyProduktu') {
                $attributes['tim_czy_magazynowy'] = (string) $classification->Codes->Code;
            }
        }
        //getting tim_jednostka_logistyczna attribute
        if ($rootNode->Packaging->ID) {
            $attributes['tim_jednostka_logistyczna'] = (string) $rootNode->Packaging->ID;
        }
        //getting tim_jednostka_miary attribute
        if ($rootNode->BaseUOMCode) {
            $attributes['tim_jednostka_miary'] = (string) $rootNode->BaseUOMCode;
        }
        //getting tim_producent attribute
        foreach ($rootNode->Packaging->Note as $note) {
            if ($note['type'] == 'SkroconaNazwaProducenta') {
                $attributes['tim_producent'] = (string) $note;
            }
        }
        //getting tim_wolumen attribute
        if ($rootNode->Packaging->PerPackageQuantity) {
            $attributes['tim_wolumen'] = (string) $rootNode->Packaging->PerPackageQuantity;
        }
        //getting sku, tim_ean, tim_nr_katalogowy_producenta attributes
        foreach ($rootNode->ItemID as $item) {
            if ($item['agencyRole'] == 'TIM article code') {
                $attributes['sku'] = (string) $item->ID;
            }
            if ($item['agencyRole'] == 'Barcode') {
                $attributes['tim_ean'] = (string) $item->ID;
            }
            if ($item['agencyRole'] == 'NrRefProducenta') {
                $attributes['tim_nr_katalogowy_producenta'] = (string) $item->ID;
            }
        }
        //getting image attribute
        foreach ($rootNode->Attachment as $attachment) {
            $attributes['image'][] = (string) $attachment->URI;
        }
        //getting description and name attributes
        foreach ($rootNode->Description as $description) {
            if ($description['type'] == 'B24') {
                $attributes['description'] = '<p>' . $description . '</p>';
            }
            if ($description['type'] == 'Nazwa') {
                $attributes['name'] = (string) $description;
            }
        }
        //getting description attribute(if exist OpisMarketingowyDlugi - it will rewrite the previous one)
        foreach ($rootNode->Description as $description) {
            if ($description['type'] == 'OpisMarketingowyDlugi') {
                $attributes['description'] = '<p>' . $description . '</p>';
            }
        }
        //getting description attribute(adding description list)
        $z = 0;
        foreach ($rootNode->Classification as $classification) {
            if ($classification['type'] == 'ETIMAttr') {
                if ($z == 0) {
                    $attributes['description'] .= '<ul>';
                }
                foreach ($classification->Note as $note) {
                    if ($note['type'] == 'Nazwa') {
                        $attributes['description'] .= '<li>' . $note . ' : ';
                    }
                }
                foreach ($classification->Note as $note) {
                    if ($note['type'] == 'Wartosc') {
                        $attributes['description'] .= $note . '</li>';
                    }
                }
                $z++;
            }
        }
        if ($z > 0) {
            $attributes['description'] .= '</ul>';
        }
        //getting weight and price attributes
        $attributes['weight'] = 1.0000;
        $attributes['price'] = 1;

        return $attributes;
    }
}