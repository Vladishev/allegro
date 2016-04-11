<?php

class Orba_Allegro_Model_Mapping_Sellform extends Mage_Core_Model_Abstract {

   
    protected function _construct() {
        $this->_init('orbaallegro/mapping_sellform');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Sellform
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Sellform_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
 
    public function getMapping(){
        $mapping = array();
        $collection = $this->getCollection()->addFieldToSelect('external_id')
                ->addFieldToSelect('local_code')
                ->addFieldToSelect('country_code');
        foreach($collection as $field){
            $mapping[$field['country_code']][$field['external_id']] = $field['local_code'];
        }
        return $mapping;
    }
	
	public function getSortOrder(){
        $sortOrder = array();
        $collection = $this->getCollection()->addFieldToSelect('external_id')
                ->addFieldToSelect('sort_order')
                ->addFieldToSelect('country_code')
				->setOrder('sort_order', 'asc');
        foreach($collection as $field){
            $sortOrder[$field['country_code']][$field['external_id']] = (int) $field['sort_order'];
        }
        return $sortOrder;
    }
	
	public function getFieldIds(){
        $fieldIds = array();
        $collection = $this->getCollection()->addFieldToSelect('external_id')
                ->addFieldToSelect('field_id')
                ->addFieldToSelect('country_code');
        foreach($collection as $field){
            $fieldIds[$field['country_code']][$field['external_id']] = (int) $field['field_id'];
        }
        return $fieldIds;
    }
}