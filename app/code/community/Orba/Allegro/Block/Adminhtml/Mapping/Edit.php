<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'mapping_id';
        $this->_blockGroup = 'orbaallegro';
        $this->_controller = 'adminhtml_mapping';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('orbaallegro')->__('Save Mapping'));
        $this->_updateButton('delete', 'label', Mage::helper('orbaallegro')->__('Delete Mapping'));
		

        if($this->_isServiceSpecified($this->getModel())){
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('orbaallegro')->__('Save And Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -100);

            $this->_formScripts[] = "
                function saveAndContinueEdit(){
                    editForm.submit($('edit_form').action+'back/edit/');
                }
            ";

        }else{
             $this->_removeButton('save');
        }
    }

    
    public function getHeaderText()
    {
        if( Mage::registry('orbaallegro_current_mapping') && Mage::registry('orbaallegro_current_mapping')->getId() ) {
            return Mage::helper('orbaallegro')->__("Edit Mapping");
        } else {
            return Mage::helper('orbaallegro')->__('Add Mapping');
        }
    }
    
    public function getModel() {
        return Mage::registry('orbaallegro_current_mapping');
    }


    public function getHeaderCssClass() {
        return 'icon-head head-categories';
    }
    
    public function getFormActionUrl() {
        return $this->getUrl('*/*/save');
    }
    
    protected function _isServiceSpecified($model=null) {
        if(is_null($this->getData('service_specified'))){
            $value = false;
            $countryCode = $this->_getService($model);
            if($countryCode){
                $model = Mage::getModel('orbaallegro/service')->load($countryCode, 'service_country_code');
                if($model->getId()){
                    $value = true;
                }
            }
            $this->setData('service_specified', $value);
        }
        return $this->getData('service_specified');
    }
    
    protected function _getService($model=null) {
        if($model && $model->getId()){
            return $model->getCountryCode();
        }
        return Mage::app()->getRequest()->getParam('country_code');
    }
}