<?php
class Orba_Allegro_Model_Resource_Service extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct() {
        $this->_init('orbaallegro/service', 'service_id');
    }    
    
}