<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Payment extends Mage_Adminhtml_Block_Widget_Grid_Container {


    public function _construct() {
        $helper = Mage::helper('orbaallegro');
        $this->_blockGroup = 'orbaallegro';
        $this->_controller = 'adminhtml_mapping_payment';
        $this->_headerText = $helper->__('Manage '.$this->getTitle().' payments mapping');
        parent::_construct();
    }

    public function _prepareLayout() {
        
        $this->setTemplate('orbaallegro/mapping/payment.phtml');
        
        $this->_removeButton("add");
        
        $this->_addButton('save_all', array(
            'label'     => Mage::helper('orbaallegro')->__('Save all'),
            'onclick'   => "paymentsForm.submit(); return false;"
        ));
        return parent::_prepareLayout();
    }
    
    public function getFormUrl() {
        return $this->getUrl("*/*/saveMapping");
    }
    
    public function getHeaderCssClass() {
        return 'icon-head head-categories';
    }
    


}