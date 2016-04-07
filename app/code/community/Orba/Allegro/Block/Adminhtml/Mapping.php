<?php

class Orba_Allegro_Block_Adminhtml_Mapping extends Mage_Adminhtml_Block_Widget_Grid_Container {

    
    public function _construct() {
        $helper = Mage::helper('orbaallegro');
        $this->_blockGroup = 'orbaallegro';
        $this->_controller = 'adminhtml_mapping';
        $this->_headerText = $helper->__('Manage '.$this->getTitle().' Mappings');
        $this->_addButtonLabel = $helper->__('Add Mapping');
        parent::_construct();
        $this->_addButton('run_all', array(
            'label'     => $helper->__('Run all mappings'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/runAll') .'\')'
        ));
    }

    public function getHeaderCssClass() {
        return 'icon-head head-categories';
    }
    
    public function getCreateUrl() {
        return $this->getUrl('*/*/new');
    }

}