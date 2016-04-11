<?php
class Orba_Allegro_Model_Resource_Mapping_Shipment_Collection 
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('orbaallegro/mapping_shipment');
    }
    
}
