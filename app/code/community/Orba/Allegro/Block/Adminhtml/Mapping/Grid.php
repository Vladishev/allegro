<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function _construct() {
        parent::_construct();
        $this->setId('mappingGrid');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $model = Mage::getModel('orbaallegro/mapping');
        /* @var $model Orba_Allegro_Model_Mapping */
        $collection = $model->getCollection();
        $attribute_code = $this->getAttributeCode();
        $collection->addFieldToFilter('attribute_code', $attribute_code);
        //$collection->addCurrentServicesFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $attribute_code = $this->getAttributeCode();
        switch ($attribute_code) {
            case Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY:
                $this->addColumn('entity_id', array(
                    'header' => Mage::helper('orbaallegro')->__('Allegro Category'),
                    'align' => 'left',
                    'index' => 'entity_id',
                    'type' => 'text',
                    'renderer' => 'Orba_Allegro_Block_Adminhtml_Mapping_Grid_Renderer_Category',
                    'filter_condition_callback' => array(
                        $this,
                        '_filterAllegroCategoriesCondition'
                    ),
                    'sortable' => false,
                ));
                $this->addColumn('entity_id_2', array(
                    'header' => Mage::helper('orbaallegro')->__('Allegro Shop Category'),
                    'align' => 'left',
                    'index' => 'entity_id_2',
                    'type' => 'text',
                    'renderer' => 'Orba_Allegro_Block_Adminhtml_Mapping_Grid_Renderer_Shopcategory',
                    'filter_condition_callback' => array(
                        $this,
                        '_filterAllegroShopCategoriesCondition'
                    ),
                    'sortable' => false,
                ));
                break;
            case Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE:
                $options = Mage::getModel('orbaallegro/mapping_source_template')->toOptionHash(false);
                $this->addColumn('entity_id', array(
                    'header' => Mage::helper('orbaallegro')->__('Default Template'),
                    'align' => 'left',
                    'index' => 'entity_id',
                    'type' => 'options',
                    'options' => $options,
                    'sortable' => true,
                ));
                break;
        }
        
        $this->addColumn('country_code', array(
            'header' => Mage::helper('orbaallegro')->__('Allegro Service'),
            'align' => 'left',
            'index' => 'country_code',
            'type' => 'options',
            'options' => Mage::getSingleton('orbaallegro/mapping_source_service')->toOptionHash(),
            'sortable' => true,
        ));
        
        /**
         * @todo modify renderer to display also default store
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $t = $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }
        $this->addColumn('priority', array(
            'header' => Mage::helper('orbaallegro')->__('Priority'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'priority',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('catalog')->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('catalog')->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'mapping_id'
                ),
                array(
                    'caption' => Mage::helper('orbaallegro')->__('Run'),
                    'url' => array(
                        'base' => '*/*/run'
                    ),
                    'field' => 'mapping_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
        ));
        return parent::_prepareColumns();
    }
    
    protected function _filterAllegroCategoriesCondition($collection, $column) {
        $value = $column->getFilter()->getValue();
        if ($value && !empty($value)) {
            $resource = Mage::getSingleton('core/resource');
            $collection->getSelect()->joinLeft( 
                array('category'=>$resource->getTableName('orbaallegro/category')), 
                'main_table.entity_id = category.category_id', 
                array('category.name')
            );
            $collection->addFieldToFilter('category.name', array('like' => '%'.$value.'%'));
        }
    }
    
    protected function _filterAllegroShopCategoriesCondition($collection, $column) {
        $value = $column->getFilter()->getValue();
        if ($value && !empty($value)) {
            $resource = Mage::getSingleton('core/resource');
            $collection->getSelect()->joinLeft( 
                array('shop_category'=>$resource->getTableName('orbaallegro/shop_category')), 
                'main_table.entity_id_2 = shop_category.category_id', 
                array('shop_category' => 'shop_category.name')
            );
            $collection->addFieldToFilter('shop_category.name', array('like' => '%'.$value.'%'));
        }
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('mapping_id' => $row->getId()));
    }

    public function getHeaderCssClass() {
        return 'icon-head head-cms-page';
    }
    
    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode() {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }
    
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }


}