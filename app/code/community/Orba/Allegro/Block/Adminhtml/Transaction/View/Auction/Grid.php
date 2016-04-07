<?php

class Orba_Allegro_Block_Adminhtml_Transaction_View_Auction_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('orbaallegro_transaction_auction_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
    }
    
    /**
     *  @return Orba_Allegro_Model_Transaction
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_transaction');
    }
    
    protected function _prepareCollection(){
        $collection = Mage::getModel('orbaallegro/transaction_auction')->getCollection();
        /* @var $collection Orba_Allegro_Model_Resource_Transaction_Auction_Collection */
        $collection->addFieldToFilter("transaction_id", $this->getModel()->getId());
        $collection->addProductData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        
        
        
        $this->addColumn('title', array(
            'header' => Mage::helper('orbaallegro')->__('Auction title'),
            'index' => 'title',
        ));
        
        $this->addColumn('sku', array(
            'header' => Mage::helper('orbaallegro')->__("SKU "),
            'index' => 'sku',
            'width' => '150px'
        ));
       
        $this->addColumn('quantity', array(
            'header' => Mage::helper('orbaallegro')->__('Requested qty'),
            'type'  => 'number',
            'width' => '100px',
            'index' => 'quantity',
        ));
        
        $this->addColumn('stock_qty', array(
            'header' => Mage::helper('orbaallegro')->__('Stock qty'),
            'type'  => 'number',
            'width' => '100px',
            'index' => 'stock_qty',
        ));
        
        
        $this->addColumn('price', array(
            'header' => Mage::helper('orbaallegro')->__('Item price'),
            'index' => 'price',
            'type'  => 'currency',
            'currency_code' => $this->getModel()->getCurrency()
        ));
        
        $this->addColumn('amount', array(
            'header' => Mage::helper('orbaallegro')->__('Amount'),
            'index' => 'amount',
            'type'  => 'currency',
            'currency_code' => $this->getModel()->getCurrency()
        ));
        
        $this->addColumn('view_auction_action', array(
            'header' => Mage::helper('orbaallegro')->__('Auction'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getAuctionId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('orbaallegro')->__('Auction'),
                    'url' => array(
                        'base' => '*/auction/view'
                    ),
                    'field' => 'auction_id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'view_auction_action',
        ));
        
        $this->addColumn('view_product_action', array(
            'header' => Mage::helper('orbaallegro')->__('Product'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getProductId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('orbaallegro')->__('Product'),
                    'url' => array(
                        'base' => '*/catalog_product/edit'
                    ),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'action',
        ));
        return parent::_prepareColumns();
    }
    

    public function getGridUrl()
    {
        return $this->getUrl('*/*/auctionGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return '#';
    }
    
    public function getRowClass($item) {
        if($item->getData("stock_qty")==$item->getData("quantity")){
            return "noticed ";
        }
        if($item->getData("stock_qty")<$item->getData("quantity")){
            return "important ";
        }
        return null;
    }
    
}