<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('auction_transaction_grid');
        $this->setDefaultSort('created_at');
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
        $collection = $this->_getAuction()->getTransactionCollection();
        $collection->addItemCount();
        $collection->addIncrementId();
        $collection->addActiveFilter();
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
        
        $this->addColumn('allegro_transaction_id', array(
            'header' => Mage::helper('orbaallegro')->__('Allegro ID'),
            'type'  => 'number',
            'width' => '100px',
            'index' => 'allegro_transaction_id',
        ));
        
        $this->addColumn('buyer_login', array(
            'header' => Mage::helper('orbaallegro')->__('Login'),
            'index' => 'buyer_login',
        ));
       
        $this->addColumn('buyer_email', array(
            'header' => Mage::helper('orbaallegro')->__('Email'),
            'index' => 'buyer_email',
        ));
       
        
        $this->addColumn('auction_item_count', array(
            'header' => Mage::helper('orbaallegro')->__('This product'),
            'type'  => 'number',
            'index' => 'auction_item_count',
            'width' => '100px',
        ));
        
        $this->addColumn('item_count', array(
            'header' => Mage::helper('orbaallegro')->__('Items count'),
            'type'  => 'number',
            'index' => 'item_count',
            'width' => '100px',
        ));        
        
        $this->addColumn('transaction_total', array(
            'header' => Mage::helper('orbaallegro')->__('Total'),
            'index' => 'transaction_total',
            'type'  => 'currency',
            'currency' => 'currency',
            'width' => '100px'
        ));       
        
        
        $this->addColumn('allegro_created_at', array(
            'header' => Mage::helper('orbaallegro')->__('Date'),
            'index' => 'allegro_created_at',
            "type"      => "datetime",
            'width' => '100px'
        ));
        
        
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('orbaallegro')->__('Order'),
            'type' => 'number',
            'align' => 'right',
            'renderer'  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_transaction_grid_column_renderer_order"),
            'index' => 'increment_id',
            'width' => '100px',
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
                        'base' => '*/transaction/view'
                    ),
                    'field' => 'transaction'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'action',
        ));

        return parent::_prepareColumns();
    }

    
    
    /**
     * @return string
     */
    public function getRowUrl($item) {
        return $this->getUrl("*/transaction/view", array('transaction'=>$item->getId()));
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridTransaction', array('_current'=>true));
    }
    
    public function getRowClass($item) {
        return Mage::helper("orbaallegro")->getTransactionRowClass($item);
    }
    
    /**
     * @return Orba_Allegro_Model_Auction
     */
    protected function _getAuction() {
        return Mage::registry('auction');
    }

}
