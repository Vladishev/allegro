<?php
class Orba_Allegro_Model_Resource_Mapping_Payment extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    public function clearAllMappings() {
        $adapter = $this->_getWriteAdapter();
        $adapter->update($this->getMainTable(), array("payment_map"=>null));
        return $this;
    }
    
    protected function _construct() {
        $this->_init('orbaallegro/mapping_payment', 'payment_id');
    } 
   
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        // Times
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        return parent::_prepareDataForSave($object);
    }
}