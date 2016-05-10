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
     * Attribute tab label
     */
    const TAB_LABEL = 'Tim Basic';

    /**
     * Cut string after set position
     * @var int
     */
    protected $_cutAfter = 50;

    /**
     * Set product weight
     * @var float
     */
    protected $_productWeight = 1.0000;

    /**
     * Set product price
     * @var int
     */
    protected $_productPrice = 1;

    /**
     * Set product quantity
     * @var int
     */
    protected $_qty = 1;

    /**
     * Attribute set id
     * @var int
     */
    protected $_attSetId;

    /**
     * Current store id
     * @var int
     */
    protected $_currStoreId;

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
                    $productStatus = '';
                    $xml = new SimpleXMLElement(file_get_contents($file));
                    $rootNode = $xml->DataArea->ItemMaster->ItemMasterHeader;
                    $itemId = $rootNode->ItemID;
                    foreach ($itemId as $item) {
                        if ($item['agencyRole'] == 'TIM article code') {
                            $sku = (string) $item->ID;
                        }
                    }

                    if (!empty($sku)) {
                        $productStatus = $this->_checkProductStatus($sku, $rootNode);
                        if ($productStatus == false) {
                            unlink($file);
                            continue;
                        }
                    } else {
                        Mage::log('File ' . $fileName . ' is broken - missing <ItemID agencyRole="TIM article code"> tag with SKU.', null, 'tim_import.log');
                        continue;
                    }

                    $categoryArray = $this->_createCategoryArray($rootNode, $fileName);
                    if ($categoryArray !== false) {
                        $categoryInfo = $this->_createCategoryTree($categoryArray, $sku);
                        if ($categoryInfo) {
                            $attributes = $this->_getAttributes($rootNode);
                            $categoryInfo['attr_set_id'] = $this->_getAttributeSetId($categoryInfo['cat_name']);
                            $this->_attributeChecking($rootNode, $categoryInfo['attr_set_id']);
                            $attributes = $this->_resizeImages($attributes);
                            $result = $this->_createProduct($attributes, $categoryInfo['cat_id']);
                            if ($result) {
                                unlink($file);
                            }
                        }
                    } else {
                        continue;
                    }
                }
                $this->_reindexData();
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
     * @param $sku
     * @return bool
     */
    protected function _createCategoryTree($categoryArray, $sku)
    {
        $categoryInfo = array();
        $result = true;
        $rootCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', 'B24');

        foreach ($categoryArray as $id => $name) {
            $currentCategory = Mage::getModel('catalog/category')->loadByAttribute('tim_category_id', $id);

            if ($currentCategory !== false) {
                $rootCategory = $currentCategory;
                $categoryInfo['cat_id'] = $id;
                $categoryInfo['cat_name'] = $name;
                continue;
            } else {
                try {
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
                    $categoryInfo['cat_id'] = $id;
                    $categoryInfo['cat_name'] = $name;
                } catch (Exception $e) {
                    Mage::log('Can not create category from file ' . $sku . '.xml. Technical details: ' . $e->getMessage(), null, 'tim_import.log');
                    $result = false;
                    break;
                }
            }
        }

        if ($result === true) {
            $categoryInfo['cat_id'] = Mage::getModel('catalog/category')
                ->loadByAttribute('tim_category_id', $categoryInfo['cat_id'])
                ->getId();
            return $categoryInfo;
        } else {
            return false;
        }

    }

    /**
     * Creates product
     * @param $attributes
     * @param $categoryId
     * @return bool
     */
    protected function _createProduct($attributes, $categoryId)
    {
        $this->_currStoreId = $storeId = Mage::app()->getStore()->getStoreId();
        $taxClass = Mage::getStoreConfig('tim_setup_tax/tim_setup_tax_group/tim_tax');
        if ($taxClass === null) {
            $taxClass = 0;
        }

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $result = true;

        try {
            $product = Mage::getModel('catalog/product')
                ->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
                ->setAttributeSetId($this->_attSetId) //ID of a attribute set
                ->setTypeId('simple') //product type
                ->setCreatedAt(strtotime('now')) //product creation time
                ->setSku($attributes['sku']) //SKU
                ->setName($attributes['name']) //product name
                ->setWeight($attributes['weight'])
                ->setStatus(1) //product status (1 - enabled, 2 - disabled)
                ->setTaxClassId($taxClass) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
                ->setPrice($attributes['price'])
                ->setDescription($attributes['description'])
                ->setShortDescription($attributes['description'])
                ->setTimCzyMagazynowy($attributes['tim_czy_magazynowy'])
                ->setTimJednostkaLogistyczna($attributes['tim_jednostka_logistyczna'])
                ->setTimJednostkaMiary($attributes['tim_jednostka_miary'])
                ->setTimProducent($attributes['tim_producent'])
                ->setTimWolumen($attributes['tim_wolumen'])
                ->setTimCrmId($attributes['tim_crm_id'])
                ->setTimNrKatalogowyProducenta($attributes['tim_nr_katalogowy_producenta'])
                ->setTimTytulAukcji($attributes['tim_tytul_aukcji'])
                ->setTimKategoriaRozmiaru($attributes['tim_kategoria_rozmiaru'])
                ->setMediaGallery(array('images'=>array (), 'values'=>array ())) //media gallery initialization
                ->setCategoryIds(array($categoryId)) //assign product to categories
                ->setStockData(array(
                        'use_config_manage_stock' => 0, //'Use config settings' checkbox
                        'manage_stock'=>1, //manage stock
                        'is_in_stock' => 1, //Stock Availability
                        'qty' => $this->_qty //qty
                    )
                );
            if (!empty($attributes['image'])) {
                $k = 0;
                foreach ($attributes['image'] as $image) {
                    if ($k == 0) {
                        $product->addImageToMediaGallery($image, array('image','thumbnail','small_image'), false, false);
                    } else {
                        $product->addImageToMediaGallery($image, null, false, false);
                    }
                    $k++;
                }
            }
            //set dynamic attributes
            if (!empty($attributes['attributes'])) {
                foreach ($attributes['attributes'] as $code => $value) {
                    $optionId = $this->_getOptionId($code, $value);
                    $product->setData($code, $optionId);
                }
            }

            $product->save();
            Mage::app()->setCurrentStore($this->_currStoreId);
        } catch (Exception $e) {
            Mage::log('Can not create product from file ' . $attributes['sku'] . '.xml. Technical details: ' . $e->getMessage(), null, 'tim_import.log');
            $result = false;
        }
        $imagesPath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'images' . DS . 'resized' . DS . '*';
        array_map("unlink", glob($imagesPath));

        return $result;
    }

    /**
     * Returns id of option item
     * @param $attribute_code
     * @param $label
     * @return string
     */
    protected function _getOptionId($attribute_code, $label)
    {
        $optionId = '';
        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_code);
        $attribute = $attribute_model->load($attribute_code);

        $options = $attribute_options_model
            ->setAttribute($attribute)
            ->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $label)
            {
                $optionId = $option['value'];
                break;
            }
        }
        return $optionId;
    }

    /**
     * Takes all needed attributes for product from xml file
     * @param $rootNode
     * @return array
     */
    protected function _getAttributes($rootNode)
    {
        $attributes = array();
        //getting tim_czy_magazynowy and tim_kategoria_rozmiaru attributes
        foreach ($rootNode->Classification as $classification) {
            if ($classification['type'] == 'KategoriaDostawcyProduktu') {
                $attributes['tim_czy_magazynowy'] = (string) $classification->Codes->Code;
            }
            if ($classification['type'] == 'KategoriaRozmiaru') {
                $attributes['tim_kategoria_rozmiaru'] = (string) $classification->Codes->Code;
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
        //getting description attribute
        foreach ($rootNode->Description as $description) {
            if ($description['type'] == 'B24') {
                $description = (string) $description;
                if (!empty($description)) {
                    $attributes['description'] = '<p>' . $description . '</p>';
                } else {
                    $attributes['description'] = '';
                }
            }

        }
        //getting tim_tytul_aukcji and name attributes
        foreach ($rootNode->Description as $description) {
            if ($description['type'] == 'Nazwa') {
                $attributes['name'] = (string) $description;
                if (strlen($attributes['name']) > $this->_cutAfter) {
                    $attributes['tim_tytul_aukcji'] = substr($attributes['name'], 0, $this->_cutAfter);
                } else {
                    $attributes['tim_tytul_aukcji'] = $attributes['name'];
                }
            }
        }
        //getting description attribute(if exist OpisMarketingowyDlugi - it will rewrite the previous one)
        foreach ($rootNode->Description as $description) {
            if ($description['type'] == 'OpisMarketingowyDlugi') {
                $description = (string) $description;
                if (!empty($description)) {
                    $attributes['description'] = '<p>' . $description . '</p>';
                } else {
                    $attributes['description'] = '';
                }
            }
        }
        //getting dynamic attributes data
        foreach ($rootNode->Classification as $classification) {
            if ($classification['type'] == 'ETIMAttr') {
                $code = (string) $classification->Codes->Code;

                foreach ($classification->Note as $note) {
                    if ($note['type'] == 'Wartosc') {
                        $attributes['attributes'][$code] = (string) $note;
                    }
                }
            }
        }
        //getting weight and price attributes
        $attributes['weight'] = $this->_productWeight;
        $attributes['price'] = $this->_productPrice;
        //getting tim_crm_id attribute
        foreach ($rootNode->ItemID as $item) {
            if ($item['agencyRole'] == 'Product ID') {
                $attributes['tim_crm_id'] = (string) $item->ID;
            } else {
                $attributes['tim_crm_id'] = '';
            }
        }

        return $attributes;
    }

    /**
     * Checks product according needed conditions
     * @param $sku
     * @param $rootNode
     * @return bool|string
     */
    protected function _checkProductStatus($sku, $rootNode)
    {
        $productStatus = '';
        foreach ($rootNode->Description as $description) {
            if ($description['type'] == 'Wyprzedaz') {
                $productStatus = (string) $description;
            }
        }
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        if ($product !== false) {
            if ($productStatus == 'TAK') {
                $productStatus = false;
            } else {
                $status = $product->getStatus();
                if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                    $storeId = 0;
                    Mage::getModel('catalog/product_status')->updateProductStatus($product->getId(), $storeId, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
                }
                $productStatus = false;
            }
        } else {
            if ($productStatus == 'TAK') {
                $productStatus = true;
            } else {
                $productStatus = false;
            }
        }
        return $productStatus;
    }

    /**
     * Download, resize and save images to tmp folder. Write new path to attributes array
     * and delete ways with unused file extensions. If file do not have allowed images,
     * sets to product alternative image from Configuration->TIM SA->Alternative image
     * @param $attributes
     * @return mixed
     */
    protected function _resizeImages($attributes)
    {
        $imagePath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'images';
        $resizedFilePath = $imagePath . DS . 'resized';
        $downloadedFilePath = $imagePath . DS . 'downloaded';
        $noImage = true;
        if (!is_dir($imagePath)) {
            mkdir($imagePath);
        }
        if (!is_dir($resizedFilePath)) {
            mkdir($resizedFilePath);
        }
        if (!is_dir($downloadedFilePath)) {
            mkdir($downloadedFilePath);
        }
        $alternativeImage = glob(Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'alternative_image' . DS . '*');
        if (!empty($alternativeImage[0])) {
            $alternativeImage = $alternativeImage[0];
        } else {
            $alternativeImage = '';
        }

        foreach ($attributes['image'] as $key => $image) {
            $fileInfo = pathinfo($image);
            if ($fileInfo['extension'] == 'jpg' || $fileInfo['extension'] == 'jpeg' || $fileInfo['extension'] == 'png') {
                $downloadedFile = $downloadedFilePath . DS . $fileInfo['basename'];
                if (!copy($image, $downloadedFile)) {
                    Mage::log('Can not download file ' . $fileInfo['basename'], null, 'tim_import.log');
                    unset($attributes['image'][$key]);
                    continue;
                }
                $imageInfo = getimagesize($downloadedFile);
                try {
                    $resizedImage = $resizedFilePath . DS . $fileInfo['basename'];
                    $imageObj = new Varien_Image($downloadedFile);
                    $imageObj->constrainOnly(true);
                    $imageObj->keepAspectRatio(true);
                    $imageObj->keepFrame(false);
                    if ($imageInfo[0] > 800) {
                        $imageObj->resize(800, null);
                    }
                    $imageObj->save($resizedImage);
                    $attributes['image'][$key] = $resizedImage;
                    $noImage = false;
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'tim_import.log');
                    unset($attributes['image'][$key]);
                }
            } else {
                unset($attributes['image'][$key]);
            }
            array_map("unlink", glob($downloadedFilePath . DS . '*'));
        }
        if ($noImage) {
            $attributes['image'][0] = $alternativeImage;
        }

        return $attributes;
    }

    /**
     * @throws Exception
     */
    protected function _reindexData()
    {
        $indexCollection = Mage::getModel('index/process')->getCollection();
        foreach ($indexCollection as $index) {
            /* @var $index Mage_Index_Model_Process */
            $index->reindexAll();
        }
    }

    /**
     * Checks is attribute set exist
     *
     * If attribute set not exist - create it
     * Returns attribute set id
     *
     * @param string $attributeSetName
     * @return mixed
     */
    protected function _getAttributeSetId($attributeSetName)
    {
        $defaultAttrSetId = Mage::getModel('catalog/product')->getDefaultAttributeSetId();
        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();
        $attributeSetId = $this->__getAttributeSetId($attributeSetName, $entityTypeId);
        if ($attributeSetId === null) {
            $attributeSet = Mage::getModel('eav/entity_attribute_set')
                ->setEntityTypeId($entityTypeId)
                ->setAttributeSetName($attributeSetName);

            $attributeSet->validate();
            $attributeSet->save();

            $attributeSet->initFromSkeleton($defaultAttrSetId)->save();
            $attributeSetId = $this->__getAttributeSetId($attributeSetName, $entityTypeId);
        }
        $this->_attSetId = $attributeSetId;

        return $attributeSetId;
    }

    /**
     * Returns attribute set id
     *
     * @param string $attributeSetName
     * @param int $entityTypeId
     * @return mixed
     */
    private function __getAttributeSetId($attributeSetName, $entityTypeId)
    {
        $attributeSetId = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->addFieldToFilter('attribute_set_name', $attributeSetName)
            ->getFirstItem()
            ->getAttributeSetId();

        return $attributeSetId;
    }

    /**
     * Get attributes from xml and check is exist
     *
     * If not exist - calls _createAttribute method
     * If exist - check if exist label in option array
     * If not - adds it
     * If yes - skip it
     *
     * @param object $rootNode SimpleXML
     * @param int $attrSetId
     */
    protected function _attributeChecking($rootNode, $attrSetId)
    {
        foreach ($rootNode->Classification as $classification) {
            if ($classification['type'] == 'ETIMAttr') {
                $attributeInfo = array();
                foreach ($classification->Note as $note) {
                    if ($note['type'] == 'Nazwa') {
                        $attributeInfo['name'] = (string) $note;
                    }
                }
                foreach ($classification->Note as $note) {
                    if ($note['type'] == 'Wartosc') {
                        $attributeInfo['value'] = (string) $note;
                    }
                }
                if (!empty($attributeInfo['name'])) {
                    $attributeInfo['code'] = (string) $classification->Codes->Code;
                    $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeInfo['code']);
                    $attributeId = (bool) $attribute->getId();
                    if ($attributeId === false) {
                        $this->_createAttribute($attributeInfo, $attrSetId);
                    } else {
                        if ($attribute->usesSource()) {
                            $isInArray = false;
                            $options = $attribute->getSource()->getAllOptions(false);
                            foreach ($options as $option) {
                                if ($option['label'] === $attributeInfo['value']) {
                                    $isInArray = true;
                                }
                            }
                            if ($isInArray === false) {
                                $attribute->setData('option', array(
                                    'value' => array(
                                        'option' => array($attributeInfo['value'], $attributeInfo['value'])
                                    )
                                ));
                                $attribute->save();
                            }
                        }
                        $setupModel = Mage::getModel('eav/entity_setup','core_setup');
                        $this->_addAttributeToSet($attrSetId, self::TAB_LABEL, $attributeInfo['code'],$setupModel);
                    }
                }
            }
        }
    }

    /**
     * Creates new product attribute based on data from xml
     *
     * @param array $attributeInfo
     * @param int $attrSetId
     */
    protected function _createAttribute($attributeInfo, $attrSetId)
    {
        $model = Mage::getModel('eav/entity_setup','core_setup');
        $data = array(
            'group' => '',
            'type' => 'varchar',
            'input' => 'select',
            'label' => $attributeInfo['name'],
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'source' => 'eav/entity_attribute_source_table',
            'required' => false,
            'is_comparable' => '0',
            'is_searchable' => '0',
            'is_unique' => '1',
            'is_configurable' => '1',
            'user_defined' => true,
            'option' => array(
                'value' => array(
                    '' => array($attributeInfo['value'])
                )
            )
        );

        $model->addAttribute(Mage_Catalog_Model_Product::ENTITY ,$attributeInfo['code'] ,$data);
        $this->_addAttributeToSet($attrSetId, self::TAB_LABEL, $attributeInfo['code'], $model);
    }

    /**
     * Adds attribute to attribute set
     *
     * @param int $attrSetId
     * @param string $group
     * @param string $attrCode
     * @param object $model Mage_Eav_Model_Entity_Setup
     */
    protected function _addAttributeToSet($attrSetId, $group, $attrCode, $model)
    {
        $entityTypeId = Mage::getModel('catalog/product')
            ->getResource()
            ->getEntityType()
            ->getId();
        $model->addAttributeToSet($entityTypeId, $attrSetId, $group, $attrCode, 20);
    }
}