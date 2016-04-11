<?php

class Orba_Allegro_Adminhtml_Mapping_ShipmentController 
    extends Orba_Allegro_Controller_Adminhtml_Abstract {

    public function indexAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('Shipments'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveMappingAction() {
        if($this->getRequest()->isPost()){
            
            $data = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('mapping'));
            
            try{
                // Clear all mappings
                Mage::getResourceModel('orbaallegro/mapping_shipment')->clearAllMappings();

                $collection = Mage::getResourceModel('orbaallegro/mapping_shipment_collection')->
                        addFieldToFilter("shipment_id", array("in"=>  array_keys($data)));
                
                foreach ($collection as $shipment){
                    /* @var $shipment Orba_Allegro_Model_Mapping_Shipment */
                    if(isset($data[$shipment->getId()]) && isset($data[$shipment->getId()]['shipment_map'])){
                        $shipment->setShipmentMap($data[$shipment->getId()]['shipment_map']);
                        
                        $shipment->save();
                        $shipment->clearInstance();
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
        $this->getLayout()->getBlock('mapping.shipment.grid')
            ->setReloadShipmentMap($this->getRequest()->getPost('reload_shipment_map', null));
        $this->renderLayout();
    }
}