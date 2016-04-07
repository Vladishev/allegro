<?php

class Orba_Allegro_Block_Adminhtml_Auction_Bid_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('orbaallegro_auction_bid_grid');
        $this->setDefaultSort('allegro_created_at');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection(){
		if (Mage::helper("orbaallegro/log")->isDebugMode()) {
			Varien_Profiler::start('orba::allegro');
		}
        $collection = Mage::getResourceModel('orbaallegro/auction_bid_collection');
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Bid_Collection */
		$collection->addFieldToSelect('bid_id');
		$collection->addFieldToSelect('buyer_login');
		$collection->addFieldToSelect('item_quantity');
		$collection->addFieldToSelect('allegro_created_at');
		$collection->addFieldToSelect('bid_status');
		$collection->addFieldToSelect('is_ignored');		
        $collection->addContractorData();
        $collection->addTotal();
        $collection->joinAuction(array("currency", "auction_title"));

		if (!Mage::helper("orbaallegro")->useAjaxForUnhandledItemCount()) {
			$collection->addUnhandledItemInfo(true);
		}		
		
        $this->setCollection($collection);
		if (Mage::helper("orbaallegro/log")->isDebugMode()) {
			Varien_Profiler::stop('orba::allegro');
		}
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        
        $this->addColumn('auction_title', array(
            'header' => Mage::helper('orbaallegro')->__('Auction'),
            'index' => 'auction_title',
        ));
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
            'header' => Mage::helper('orbaallegro')->__('Count total'),
            'index' => 'item_quantity',
        ));

		if (Mage::helper("orbaallegro")->useAjaxForUnhandledItemCount()) {
			$this->addColumn('unhandled_item_count', array(
				'header'	=> Mage::helper('orbaallegro')->__('Unhandled items'),
				'width'		=> '100px',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'renderer'	=> Mage::getConfig()->getBlockClassName("orbaallegro/adminhtml_transaction_grid_column_renderer_unhandled"),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'unhandled_item_count',
				'align'		=> 'center',
			));
		} else {
			$this->addColumn('unhandled_item_count', array(
				'type'  => 'number',
				'header' => Mage::helper('orbaallegro')->__('Unhandled items'),
				'index' => 'unhandled_item_count',
			));
		}
        
        $this->addColumn('total', array(
            'type'  => 'currency',
            'header' => Mage::helper('orbaallegro')->__('Total'),
            'index' => 'total',
            'currency'=> 'currency',
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
        
        $this->addColumn('is_ignored', array(
            'header' => Mage::helper('orbaallegro')->__('Ignored'),
            'type' => 'options',
            'index' => 'is_ignored',
            'options' => Mage::getSingleton('orbaallegro/misc_yesno')->toOptionHash(),
            'width' => '50px',
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

    public function getRowUrl($row){
        return $this->getUrl('*/*/view', array('bid_id'=>$row->getId()));
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

}