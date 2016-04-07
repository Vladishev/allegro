<?php
class Orba_Allegro_Block_Cms_Block extends Mage_Cms_Block_Block
{
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $storeId = $this->getStore()->getId();
            $block = Mage::getModel('cms/block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                $processor = Mage::getModel("orbaallegro/template_filter");
                /* @var $processor Orba_Allegro_Model_Template_Filter */
                $processor->setStoreId($storeId);
                $processor->setProduct($this->getProduct());
                $processor->setUser($this->getUser());
                $html = $processor->filter($block->getContent());
            }
        }
        return $html;
    }
    
    /** @todo Add lazy loading below **/
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        return Mage::registry("product") ? Mage::registry("product") : Mage::getModel("catalog/product");
    }
    
    /**
     * @return Orba_Allegro_Model_User
     */
    public function getUser() {
        return Mage::registry("user") ? Mage::registry("user") : Mage::getModel("orbaallegro/user");
    }
    
    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore() {
         return Mage::registry("store") ? Mage::registry("store") : Mage::app()->getStore();
    }
    

}
