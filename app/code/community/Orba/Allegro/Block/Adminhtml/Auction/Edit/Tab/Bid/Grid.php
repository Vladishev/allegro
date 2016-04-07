<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Bid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('auction_bid_grid');
        $this->setDefaultSort('allegro_created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_getAuction()->getBidCollection();
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Bid_Collection */
        $collection->addUnhandledItemInfo(true);
        $collection->addContractorData();
        $collection->addTotal();
        $this->setCollection($collection);
        
        parent::_prepareCollection();
        return $this;
    }


    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn('buyer_login', array(
            'header' => Mage::helper('orbaallegro')->__('Login'),
            'index' => 'buyer_login',
        ));
        
        $this->addColumn('buyer_email', array(
            'header' => Mage::helper('orbaallegro')->__('Email'),
            'index' => 'email',
        ));
        
        $this->addColumn('item_quantity', array(
            'type'  => 'number',
            'header' => Mage::helper('orbaallegro')->__('This product'),
            'index' => 'item_quantity',
        ));
        
        $this->addColumn('unhandled_item_count', array(
            'type'  => 'number',
            'header' => Mage::helper('orbaallegro')->__('Unhandled items'),
            'index' => 'unhandled_item_count',
        ));
        
        $this->addColumn('total', array(
            'type'  => 'currency',
            'header' => Mage::helper('orbaallegro')->__('Total'),
            'index' => 'total',
            'currency_code'=> $this->_getAuction()->getCurrency(),
            'width' => '100px',
        ));
        
        $this->addColumn('allegro_created_at', array(
            'header'=> Mage::helper('orbaallegro')->__('Date'),
            'index' => 'allegro_created_at',
            "type"  => "datetime",
            'width' => '100px'
        ));
        
        $this->addColumn('bid_status', array(
            'header'=> Mage::helper('orbaallegro')->__('Bid status'),
            'index' => 'bid_status',
            "type"  => "options",
            'width' => '100px',
            "options" => Mage::getSingleton("orbaallegro/auction_bid_status")->toOptionHash()
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('orbaallegro')->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('orbaallegro')->__('View'),
                    'url' => array(
                        'base' => '*/auction_bid/view'
                    ),
                    'field' => 'bid_id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'action',
        ));

        return parent::_prepareColumns();
    }

    
    public function getRowClass($item) {
        $classes = array();
        if((int)$item->getData("unhandled_item_count")>0 && !$item->getIsIgnored() &&
           $item->getBidStatus()==Orba_Allegro_Model_Auction_Bid::STATUS_BID_SOLD){
            $classes[] = "noticed";
        }
        if($this->getIsDeleted()){
            $classes[] = "striked";
        }
        if($item->getIsIgnored()){
            $classes[] = "ignored";
        }
        return count($classes) ? join(" ", $classes) : null;
    }

    
    /**
     * @return string
     */
    public function getRowUrl($item) {
        return $this->getUrl("*/auction_bid/view", array('bid_id'=>$item->getId()));
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridBid', array('_current'=>true));
    }
    
    /**
     * @return Orba_Allegro_Model_Auction
     */
    protected function _getAuction() {
        return Mage::registry('auction');
    }

}
