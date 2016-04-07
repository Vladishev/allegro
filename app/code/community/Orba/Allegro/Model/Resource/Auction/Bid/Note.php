<?php
class   Orba_Allegro_Model_Resource_Auction_Bid_Note extends 
    Mage_Core_Model_Resource_Db_Abstract {
  
    protected function _construct() {
        $this->_init('orbaallegro/auction_bid_note', 'note_id');
    } 
    
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object){
        // Times
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        return parent::_prepareDataForSave($object);
    }
}