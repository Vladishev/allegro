<?php

class Orba_Allegro_Adminhtml_ContractorController extends Orba_Allegro_Controller_Adminhtml_Abstract {

    public function viewAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = $this->_registerModel();
        
        if(!$model->getId()){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Contractor dont exists"));
            return $this->_redirectReferer();
        }
        
        $this->_title($this->__('Sales'))
                ->_title($this->__('Allegro.pl'))
                ->_title($this->__('View contractor'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * @return Orba_Allegro_Model_Contractor
     */
    public function _registerModel() {
        if(!Mage::registry('orbaallegro_current_contractor')){
            $model = Mage::getModel('orbaallegro/contractor');
            if ($id = $this->getRequest()->getParam('contractor')) {
                $model->load($id);
            }
            Mage::register('orbaallegro_current_contractor', $model);
        };
        return Mage::registry('orbaallegro_current_contractor');
    }

}