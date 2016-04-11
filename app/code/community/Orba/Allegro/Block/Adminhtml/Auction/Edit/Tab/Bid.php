<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Bid
    extends Mage_Adminhtml_Block_Widget_Container
{
    
    public function __construct() {
        $this->setTemplate('orbaallegro/auction/edit/tab/bid.phtml');
        parent::__construct();
    }
    
    public function _prepareLayout() {
        $this->setChild('auction_bid_grid', 
            $this->getLayout()->
                createBlock(
                    'orbaallegro/adminhtml_auction_edit_tab_bid_grid', 
                    'orbaallegro_auction_transaction_bid'
                )->
                setAuction($this->getAuction())
        );
        parent::_prepareLayout();
    }
}
