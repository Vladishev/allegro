<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Template extends Orba_Allegro_Block_Adminhtml_Mapping {

    public function _construct() {
        parent::_construct();
        $this->_headerText = Mage::helper('orbaallegro')->__('Templates Mappings');
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->getChild('grid')->setAttributeCode(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
    }

}