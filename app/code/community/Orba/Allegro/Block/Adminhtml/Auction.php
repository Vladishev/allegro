<?php
class Orba_Allegro_Block_Adminhtml_Auction extends Mage_Adminhtml_Block_Widget_Container {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('orbaallegro/auction.phtml');
    }

    protected function _prepareLayout() {
        $this->_addButton('refresh_statuses', array(
            'label'   => $this->__('Refresh latest auctions'),
            'onclick' => "setLocation('{$this->getUrl('*/*/refresh')}')",
            'class'   => 'refresh'
        ));
        $this->_addButton('add_new', array(
            'label'   => $this->__('Create auction'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));
        $this->setChild('grid', $this->getLayout()->createBlock('orbaallegro/adminhtml_auction_grid', 'orbaallegro_auction_grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml() {
        return $this->getChildHtml('add_new_button');
    }
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
    
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
}
