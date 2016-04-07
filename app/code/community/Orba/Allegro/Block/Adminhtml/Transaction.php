<?php
class Orba_Allegro_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Widget_Container {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('orbaallegro/transaction.phtml');
    }

    protected function _prepareLayout() {
        $this->_addButton('refresh_statuses', array(
            'label'   => $this->__('Refresh'),
            'onclick' => "setLocation('{$this->getUrl('*/*/refresh')}')",
            'class'   => 'refresh'
        ));
        $this->setChild('grid', $this->getLayout()->createBlock('orbaallegro/adminhtml_transaction_grid', 'orbaallegro_transaction_grid'));
        return parent::_prepareLayout();
    }
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
    
//    public function isSingleStoreMode()
//    {
//        if (!Mage::app()->isSingleStoreMode()) {
//               return false;
//        }
//        return true;
//    }
}
