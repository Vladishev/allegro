<?php
class Orba_Allegro_Block_Adminhtml_Catalog_Product_Grid_Column_Renderer_Auction
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
        
    protected $_services = array();
    
    public function render(Varien_Object $row)
    {
        if(!$row->getId() || !in_array($row->getTypeId(), Mage::helper("orbaallegro")->getAllowedAuctionProductTypes())){
            return "";
        }
        
        $params = array(
            'product' => $row->getId()
        );
        
        if(!is_null($categoryId=$this->getCategoryId($row))){
            $params['category'] = (int)$categoryId;
        }
        
        if(!is_null($shopCategoryId=$this->getShopCategoryId($row))){
            $params['shop_category'] = (int)$shopCategoryId;
        }
        
        if(!is_null($templateId=$this->getTemplateId($row))){
            $params['template'] = (int)$templateId;
        }
        
        if(!is_null($store=Mage::app()->getRequest()->getParam('store'))){
            $params['store'] = (int)$store;
        }
        
        
        return '<a href="'.$this->getUrl("*/auction/new", $params).'">'.
                Mage::helper("orbaallegro")->__("New Auction").
               '</a>';
    }
    
    public function getShopCategoryId(Mage_Catalog_Model_Product $product) {
        $this->_getMapping()->validShopCategoryId($product);
        return $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY);
    }
    
    public function getCategoryId(Mage_Catalog_Model_Product $product) {
        $this->_getMapping()->validCategoryId($product);
        return $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY);
    }
    
    public function getTemplateId(Mage_Catalog_Model_Product $product) {
        $this->_getMapping()->validTempalteId($product);
        return $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
    }

    /**
     * @return Orba_Allegro_Model_Mapping
     */
    protected function _getMapping() {
        return Mage::getSingleton("orbaallegro/mapping");
    }
}
