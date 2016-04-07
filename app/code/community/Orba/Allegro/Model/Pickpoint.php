<?php

class Orba_Allegro_Model_Pickpoint extends Mage_Core_Model_Abstract {
 
    protected function _construct() {
        $this->_init('orbaallegro/pickpoint');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Pickpoint
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Pickpoint_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
    public function getIdByAttributes($attributes){
        $collection = $this->getCollection();
        foreach($attributes as $attribute => $value)
            $collection->addFieldToFilter($collection);
        $first = $collection->getFirstItem();
        if ($first->getId()) {
            return $first->getId();
        }
        return false; 
    }
    
    public function loadByAttribute($attribute, $value) {
        $collection = $this->getCollection()
            ->addFieldToFilter($attribute, $value);
        $first = $collection->getFirstItem();
        if ($first->getId()) {
            return $first;
        }
        return false;
    }
}