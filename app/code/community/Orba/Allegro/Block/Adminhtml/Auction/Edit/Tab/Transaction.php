<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Transaction
    extends Mage_Adminhtml_Block_Widget_Container
{
    
    public function __construct() {
        $this->setTemplate('orbaallegro/auction/edit/tab/transaction.phtml');
        parent::__construct();
    }
    
    public function _prepareLayout() {
        $this->setChild('auction_transaction_grid', 
            $this->getLayout()->
                createBlock(
                    'orbaallegro/adminhtml_auction_edit_tab_transaction_grid', 
                    'orbaallegro_auction_transaction_grid'
                )->
                setAuction($this->getAuction())
        );
        parent::_prepareLayout();
    }
}
