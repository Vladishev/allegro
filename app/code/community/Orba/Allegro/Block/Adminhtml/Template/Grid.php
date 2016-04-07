<?php

class Orba_Allegro_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('orbaallegro_template_grid');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('desc');
    }
    
    protected function _prepareCollection(){
        $collection = Mage::getModel('orbaallegro/template')->getCollection();
        /* @var $collection Orba_Allegro_Model_Resource_Template_Collection */
        $collection->addAttributeToSelect(array("status"));
        $store = $this->_getStore();
        
        $collection->setStore($store);
        
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            //$collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'orbaallegro_template/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'orbaallegro_template/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
        }else{
            $collection->addAttributeToSelect(array("name"));
        }
        
        $collection->addProductCount();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        $store = $this->_getStore();
        
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('orbaallegro')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('orbaallegro')->__('Name'),
            'index' => 'name',
        ));
        
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('orbaallegro')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }
        
        $this->addColumn('country_code', array(
            'header' => Mage::helper('orbaallegro')->__('Service'),
            'index' => 'country_code',
            'type'  => 'options',
            'index' => 'country_code',
            'width' => '100px',
            'options' => Mage::getSingleton('orbaallegro/system_config_source_service')->toOptionHash(),
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('orbaallegro')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'index' => 'status',
            'width' => '100px',
            'options' => Mage::getSingleton('orbaallegro/template_status')->getOptionArray(),
        ));
        
        $this->addColumn('product_count', array(
            'header' => Mage::helper('orbaallegro')->__('Assigments'),
            'index' => 'product_count',
            'type' => 'number'
        ));
        
        $this->addColumn('action', array(
            'header' => Mage::helper('orbaallegro')->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('orbaallegro')->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit',
                        'params'=>array('store'=>$this->getRequest()->getParam('store'))
                    ),
                    'field' => 'entity_id'
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
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'entity_id'=>$row->getId())
        );
    }
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
}