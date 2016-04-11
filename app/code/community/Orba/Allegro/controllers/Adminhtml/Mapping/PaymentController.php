<?php

class Orba_Allegro_Adminhtml_Mapping_PaymentController 
    extends Orba_Allegro_Controller_Adminhtml_Abstract {

    public function indexAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('Payments'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveMappingAction() {
        if($this->getRequest()->isPost()){
            
            $data = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('mapping'));
            
            try{
                // Clear all mappings
                Mage::getResourceModel('orbaallegro/mapping_payment')->clearAllMappings();

                $collection = Mage::getResourceModel('orbaallegro/mapping_payment_collection')->
                        addFieldToFilter("payment_id", array("in"=>  array_keys($data)));
                
                foreach ($collection as $payment){
                    /* @var $payment Orba_Allegro_Model_Mapping_Payment */
                    if(isset($data[$payment->getId()]) && isset($data[$payment->getId()]['payment_map'])){
                        $payment->setPaymentMap($data[$payment->getId()]['payment_map']);
                        $payment->save();
                        $payment->clearInstance();
                    }
                }
                $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__("Mappings saved."));
            }  catch (Exception $e){
                $this->_getSession()->addError($e->getMessage());
            }
        }
        return $this->_redirectReferer();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('mapping.payment.grid')
            ->setReloadPaymentMap($this->getRequest()->getPost('reload_payment_map', null));
        $this->renderLayout();
    }
}