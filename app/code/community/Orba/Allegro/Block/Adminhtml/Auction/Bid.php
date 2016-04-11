<?php
class Orba_Allegro_Block_Adminhtml_Auction_Bid extends Mage_Adminhtml_Block_Widget_Container {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('orbaallegro/auction/bid.phtml');
    }

    protected function _prepareLayout() {
        $this->_addButton('refresh_statuses', array(
            'label'   => $this->__('Refresh'),
            'onclick' => "setLocation('{$this->getUrl('*/*/refresh')}')",
            'class'   => 'refresh'
        ));
        $this->setChild('grid', $this->getLayout()->
                createBlock('orbaallegro/adminhtml_auction_bid_grid', 'orbaallegro_auction_bid_grid'));
        return parent::_prepareLayout();
    }

    public function getRefreshButtonHtml() {
        return $this->getChildHtml('refresh_statuses');
    }
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
}
