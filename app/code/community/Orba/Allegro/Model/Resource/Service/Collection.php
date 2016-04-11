<?php
class Orba_Allegro_Model_Resource_Service_Collection extends
    Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    protected function _construct(){
        parent::_construct();
        $this->_init('orbaallegro/service');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Service_Collection
     */
    public function addAvailableFilter() {
        $this->addFieldToFilter(
            array("installed_from_api", "is_production"),
            array(
                array("eq"=>1),
                array("eq"=>0)
            )
        );
        $this->addFieldToFilter('is_supported', 1);
        return $this;
    }
    
    public function toOptionArray() {
        return parent::_toOptionArray('service_country_code', 'service_name');
    }
    
    public function toOptionHash() {
        return parent::_toOptionHash('service_country_code', 'service_name');
    }
}
