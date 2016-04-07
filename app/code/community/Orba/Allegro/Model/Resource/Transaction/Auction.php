<?php
class Orba_Allegro_Model_Resource_Transaction_Auction extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct() {
        $this->_init('orbaallegro/transaction_auction', 'item_id');
    } 
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        if($object->getId()){
            $object->setCreatedAt();
        }
        parent::_beforeSave($object);
    }
   
    /**
     * Set times
     * @param Mage_Core_Model_Abstract $object
     * @return type
     */
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