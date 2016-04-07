<?php
class Orba_Allegro_Model_System_Config_Source_Attribute_Product_Image 
    extends Orba_Allegro_Model_System_Config_Source_Attribute_Abstract {
    
    
    public function getAllOptions() {
        $entity_type_id = $this->_getEntityTypeId("catalog_product");
        if ($entity_type_id) {
            $attributes_collection = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->addFieldToFilter("frontend_input", "media_image")
                    ->addFieldToFilter('is_visible', 1);
            $res = array();
            foreach ($attributes_collection as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                $res[$attribute->getAttributeCode()] = array(
                    'label' => $attribute->getStoreLabel($this->getStore()),
                    'value' => $attribute->getAttributeCode()
                );
            }
            ksort($res);
        }
        return $res;
    }
}