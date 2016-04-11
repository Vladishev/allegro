<?php

class Orba_Allegro_Model_Mapping extends Mage_Rule_Model_Rule {

    
    const ATTR_CODE_USE_MAPPING     = "orbaallegro_use_mapping";
    const ATTR_CODE_SHOP_CATEGORY   = "orbaallegro_shop_category_id";
    const ATTR_CODE_CATEGORY        = "orbaallegro_category_id";
    const ATTR_CODE_TEMPLATE        = "orbaallegro_template_id";
    
    protected $_conditions;
    protected $_productIds;
    protected $_product;
    
    protected $_validTemplates = array();
    protected $_validCategories = array();
    protected $_validShopCategories = array();

    protected function _construct() {
        $this->_init('orbaallegro/mapping');
    }
    
    protected function getConfig() {
        return Mage::getModel('orbaallegro/config');
    }

    public function getConditionsInstance() {
        return Mage::getModel('orbaallegro/mapping_condition_combine');
    }

    public function getConditions() {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }
        return $this->_conditions;
    }


    protected function _afterLoad() {
        $conditions_arr = unserialize($this->getConditionsSerialized());
        if (!empty($conditions_arr) && is_array($conditions_arr)) {
            $this->getConditions()->loadArray($conditions_arr);
        }
        return parent::_afterLoad();
    }

    public function getMatchingProductIds() {
        $this->_productIds = array();
        $this->setCollectedAttributes(array());
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addAttributeToFilter(self::ATTR_CODE_USE_MAPPING, 1);
        $this->getConditions()->collectValidatedAttributes($productCollection);
        Mage::getSingleton('core/resource_iterator')->walk(
            $productCollection->getSelect(), array(array($this, 'callbackValidateProduct')), array(
                'attributes' => $this->getCollectedAttributes(),
                'product' => Mage::getModel('catalog/product'),
            )
        );
        unset($productCollection);
        return $this->_productIds;
    }

    public function callbackValidateProduct($args) {
        $product = clone $args['product'];
        $product->setData($args['row']);
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
        unset($product);
        unset($args);
    }

    public function run($load = false, $reindex = true) {
        ini_set('max_execution_time', 0);
        if ($load) {
            $this->load($load);
        }
        if ($this->getId()) {
            $matched_product_ids = $this->getMatchingProductIds();
            $_product = Mage::getSingleton('orbaallegro/catalog_product');
            
            // First action
            $res = $_product->update($matched_product_ids, $this->getAttributeCode(), $this->getEntityId(), $this->getStores());
            
            // Second action (optiopnal)
            if(($attributeCode2 = $this->getData('attribute_code_2')) && ($entityId2 = $this->getData('entity_id_2'))){
                $res = $res && $_product->update($matched_product_ids, $attributeCode2, $entityId2, $this->getStores());
            }
            
            
            unset($matched_product_ids);
            unset($_product);
            if ($reindex) {
                foreach($this->getStores() as $storeId){
                    $this->reindexFlatCatalogIfNeccesary($storeId);
                }
            }
            return $res;
        }
        return false;
    }

    public function runAll($attribute_code) {
        $collection = $this->getCollection()
                ->setOrder('priority', 'ASC')
                ->addFieldToFilter('attribute_code', $attribute_code);
        $collection->count();
        $error = false;
        $ids = array();
        $stores = array();
        foreach ($collection as $key => $mapping) {
            $ids[] = $mapping->getId();
        }
        unset($collection);
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $m = Mage::getModel('orbaallegro/mapping')->load($id);
                if (!$m->run($id, false)) {
                    $error = true;
                    break;
                }
                $stores = array_merge($stores, $m->getStores());
                unset($m);
            }
        }
        foreach(array_unique($stores) as $storeId){
            $this->reindexFlatCatalogIfNeccesary($storeId);
        }
        return !$error;
    }
    
    public function cronRunAll() {
        return $this->runAll(self::ATTR_CODE_CATEGORY) && $this->runAll(self::ATTR_CODE_SHOP_CATEGORY) && $this->runAll(self::ATTR_CODE_TEMPLATE);
    }
    
    protected function reindexFlatCatalogIfNeccesary($storeId) {
        if ($this->getConfig()->isFlatCatalogEnabled($storeId)) {
            $process = Mage::getModel('index/process')->load(4);
            $process->reindexAll();
        }
    }

    /**
     * @return Orba_Allegro_Model_Resource_Mapping_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
    public function validTempalteId(Mage_Catalog_Model_Product $product) {
         $cc = Mage::getSingleton("orbaallegro/config")->
                getCountryCode($product->getStoreId());
         $templateAttr = Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE;
         if($templateId = $product->getData($templateAttr)){
             if(!isset($this->_validTemplates[$templateId])){
                 $this->_validTemplates[$templateId] = 
                    Mage::getModel("orbaallegro/template")->load($templateId);
             }
            $template = $this->_validTemplates[$templateId] ;
            if($template->getCountryCode()!=$cc){
                $product->unsetData($templateAttr);
            }else{
                $product->setAllegroTemplate($template);
            }
        }
        return $product;
    }
    
    public function validCategoryId(Mage_Catalog_Model_Product $product) {
         $cc = Mage::getSingleton("orbaallegro/config")->
                getCountryCode($product->getStoreId());       
         $categoryAttr = Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY;
         if($categoryId = $product->getData($categoryAttr)){
             if(!isset($this->_validCategories[$categoryId])){
                 $this->_validCategories[$categoryId] = 
                    Mage::getModel("orbaallegro/category")->load($categoryId);
             }
            $category =  $this->_validCategories[$categoryId] ;
            if($category->getCountryId()!=$cc){
                $product->unsetData($categoryAttr);
            }else{
                $product->setAllegroCategory($category);
            }
        }
        return $product;
    }
        
    public function validShopCategoryId(Mage_Catalog_Model_Product $product) {
         $config =  Mage::getSingleton("orbaallegro/config");
         $cc = $config->getCountryCode($product->getStoreId());   
         $login = $config->getLogin($product->getStoreId());
         $categoryAttr = Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY;
         if($categoryId = $product->getData($categoryAttr)){
             if(!isset($this->_validShopCategories[$categoryId])){
                 $this->_validShopCategories[$categoryId] = 
                    Mage::getModel("orbaallegro/shop_category")->load($categoryId);
             }
            $category =  $this->_validShopCategories[$categoryId] ;
            if($category->getCountryId()!=$cc || $category->getUserLogin() != $login){
                $product->unsetData($categoryAttr);
            }else{
                $product->setAllegroShopCategory($category);
            }
        }
        return $product;
    }
}