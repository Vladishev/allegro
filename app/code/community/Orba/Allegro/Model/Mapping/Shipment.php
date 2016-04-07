<?php

class Orba_Allegro_Model_Mapping_Shipment extends Mage_Core_Model_Abstract {

    const TYPE_PREPAY = 1;
    const TYPE_COD = 2;

    protected function _construct() {
        $this->_init('orbaallegro/mapping_shipment');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Shipment
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Shipment_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
}