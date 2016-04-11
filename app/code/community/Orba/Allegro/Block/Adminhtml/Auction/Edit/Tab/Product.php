<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Product
    extends Mage_Adminhtml_Block_Widget_Container
{
    
    public function __construct() {
        $this->setTemplate('orbaallegro/auction/edit/tab/product.phtml');
        parent::__construct();
    }
    
    public function _prepareLayout() {
        $this->setChild('auction_product_grid', 
                $this->getLayout()->createBlock(
                    'orbaallegro/adminhtml_auction_edit_tab_product_grid', 
                    'orbaallegro_auction_product_grid'
        ));
        parent::_prepareLayout();
    }
}
