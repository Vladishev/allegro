<?php

class Orba_Allegro_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('orbaallegro_transaction_grid');
        $this->setDefaultSort('allegro_transaction_id');
        $this->setDefaultDir('desc');
    }
    
    protected function _prepareCollection(){
        $collection = Mage::getModel('orbaallegro/transaction')->getCollection();
        /* @var $collection Orba_Allegro_Model_Resource_Transaction_Collection */
        $collection->addItemCount();
        $collection->addIncrementId();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        
//        $this->addColumn('transaction_id', array(
//            'header' => Mage::helper('orbaallegro')->__('ID'),
//            'type'  => 'number',
//            'width' => '100px',
//            'index' => 'transaction_id',
//        ));
        
        
        $this->addColumn('allegro_transaction_id', array(
            'header' => Mage::helper('orbaallegro')->__('Transaction ID'),
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
       
        
        $this->addColumn('item_count', array(
            'header' => Mage::helper('orbaallegro')->__('Items'),
            'type'  => 'number',
            'index' => 'item_count',
        ));
        
        $this->addColumn('transaction_subtotal', array(
            'header' => Mage::helper('orbaallegro')->__('Subtotal'),
            'index' => 'transaction_subtotal',
            'type'  => 'currency',
            'currency' => 'currency',
            'renderer'  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_transaction_grid_column_renderer_currency")
        ));
        
        $this->addColumn('transaction_cod_amount', array(
            'header'    => Mage::helper('orbaallegro')->__('Shipping cost'),
            'index'     => 'transaction_cod_amount',
            'type'      => 'currency',
            'currency'  => 'currency',
            'renderer'  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_transaction_grid_column_renderer_currency")
        ));
        
        $this->addColumn('transaction_total', array(
            'header' => Mage::helper('orbaallegro')->__('Total'),
            'index' => 'transaction_total',
            'type'  => 'currency',
            'currency' => 'currency',
            'renderer'  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_transaction_grid_column_renderer_currency")
        ));
        
        $this->addColumn('country_code', array(
            'header' => Mage::helper('orbaallegro')->__('Service'),
            'index' => 'country_code',
            'type'  => 'options',
            'index' => 'country_code',
            'width' => '100px',
            'options' => Mage::getSingleton('orbaallegro/system_config_source_service')->toOptionHash(),
        ));
        
        $this->addColumn('allegro_created_at', array(
            'header' => Mage::helper('orbaallegro')->__('Date'),
            'index' => 'allegro_created_at',
            "type"  => "datetime",
            'width' => '100px',
        ));
        
        $this->addColumn('is_deleted', array(
            'header' => Mage::helper('orbaallegro')->__('Expired'),
            'type' => 'options',
            'index' => 'is_deleted',
            'options' => Mage::getSingleton('orbaallegro/misc_yesno')->toOptionHash(),
            'width' => '50px',
        ));
        
        $this->addColumn('is_ignored', array(
            'header' => Mage::helper('orbaallegro')->__('Ignored'),
            'type' => 'options',
            'index' => 'is_ignored',
            'options' => Mage::getSingleton('orbaallegro/misc_yesno')->toOptionHash(),
            'width' => '50px',
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
                        'base' => '*/*/view'
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
    

    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('transaction'=>$row->getId()));
    }
    
    public function getRowClass($item) {
        return Mage::helper("orbaallegro")->getTransactionRowClass($item);
    }
    
}