<?php

class Orba_Allegro_Model_Mapping_Payment extends Mage_Core_Model_Abstract {

   
    protected function _construct() {
        $this->_init('orbaallegro/mapping_payment');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Payment
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Payment_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
}