<?php
class Orba_Allegro_Model_Mapping_Source_Template {
    
    public function getAllOptions() {
        return $this->_getCollection()->toOptionArray();
    }
    
    public function toOptionHash() {
        return $this->_getCollection()->toOptionHash();
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Template_Collection
     */
    protected function _getCollection() {
        return Mage::getResourceModel("orbaallegro/template_collection");
    }
}

?>
