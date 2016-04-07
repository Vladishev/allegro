<?php
class Orba_Allegro_Block_Adminhtml_Contractor_View extends Mage_Adminhtml_Block_Widget {

    /**
     *  @return Orba_Allegro_Model_Contractor
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_contractor');
    }

    protected function _prepareLayout() {
        
        $transactionId = Mage::app()->getRequest()->getParam("transaction");
        
        if($transactionId){
            $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Back'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/transaction/view', array("transaction"=>$transactionId)) . "'",
                    'class' => 'back'
                ))
            );  
        }
        
        $this->setChild('refresh',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Refresh'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*/refresh', array("_current"=>true)) . "'",
                ))
        );  
        
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml() {
        return $this->getChildHtml('back_button');
    }
    
    
    public function getRefreshButton() {
        return $this->getChildHtml('refresh');
    }
    
    public function getHeaderText() {
        return  Mage::helper('orbaallegro')->__('View contractor') . " " . $this->getModel()->getLogin();
    }
    
    public function getCountryLabel($coutryCode) {
        return Mage::helper('orbaallegro')->getCountryLabel($coutryCode);
    }
    
}
