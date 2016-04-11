<?php
class Orba_Allegro_Model_Catalog_Product_Attribute_Source_Option_Template
    extends Orba_Allegro_Model_Catalog_Product_Attribute_Source_Option_Abstract{
    
    
    /**
     * @todo Implement
     */
    public function getAllOptions() {
        $cc = Mage::getSingleton('orbaallegro/config')->getCountryCode(
                Mage::app()->getRequest()->getParam('store'),
                Mage::app()->getRequest()->getParam('website')
        );
        $coll = Mage::getResourceModel("orbaallegro/template_collection");
        /* @var $coll Orba_Allegro_Model_Resource_Template_Collection */
        $coll->addAttributeToFilter('country_code', $cc);
        $out = array(''=>Mage::helper('orbaallegro')->__('No template'));
        foreach($coll->toOptionHash() as $k=>$v){
            $out[$k]=$v;
        }
        return $out;
    }
    
}
