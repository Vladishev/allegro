<?php
abstract class Orba_Allegro_Model_System_Config_Source_Attribute_Abstract 
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
   
    public function toOptionArray() {
        return $this->getAllOptions();
    }
    
    protected function _getEntityTypeId($code) {
        $collection = Mage::getModel('eav/entity_type')->getCollection()
                ->addFieldToFilter('entity_type_code', $code);
        $item = $collection->getFirstItem();
        return $item->getId();
    }

    
}