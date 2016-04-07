<?php

class Orba_Allegro_Block_Adminhtml_Auction_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('orbaallegro_auction_grid');
        $this->setDefaultSort('auction_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection(){
        $collection = Mage::getResourceModel('orbaallegro/auction_collection');
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
		$collection->addFieldToSelect('auction_id');
		$collection->addFieldToSelect('allegro_auction_id');
		$collection->addFieldToSelect('auction_title');
		$collection->addFieldToSelect('category_id');
		$collection->addFieldToSelect('items_placed');
		$collection->addFieldToSelect('items_sold');
		$collection->addFieldToSelect('auction_item_price');
		$collection->addFieldToSelect('auction_status');
		$collection->addFieldToSelect('auction_additional_options');
		$collection->addFieldToSelect('store_id');
		$collection->addFieldToSelect('country_code');
        $collection->addStockQty();
        $collection->addEndTime();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("auction_id", array(
            "index"     =>"auction_id",
            "header"    => Mage::helper("orbaallegro")->__("Local ID"),
            "align"     => "right",
            "type"      => "number",
            "width"     => "100px"
        ));
        $this->addColumn("allegro_auction_id", array(
            "index"     =>"allegro_auction_id",
            "header"    => Mage::helper("orbaallegro")->__("Allegro ID"),
            "align"     => "right",
            "type"      => "number",
            "width"     => "100px"
        ));
        $this->addColumn("auction_title", array(
            "index"     =>"auction_title",
            "header"    => Mage::helper("orbaallegro")->__("Title"),
        ));
        $this->addColumn('category_id', array(
            'header' => Mage::helper('orbaallegro')->__('Allegro Category'),
            'align' => 'left',
            'index' => 'category_id',
            'type' => 'text',
            'renderer' => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_auction_grid_column_renderer_category"),
            'filter_condition_callback' => array(
                $this,
                '_filterAllegroCategoriesCondition'
            ),
            'sortable' => false,
        ));
        $this->addColumn("items_placed", array(
            "index"     => "items_placed",
            "type"      => "number",
            "header"    => Mage::helper("orbaallegro")->__("Items placed"),
            "width"     => "100px"
        ));
        $this->addColumn("items_sold", array(
            "index"     => "items_sold",
            "type"      => "number",
            "header"    => Mage::helper("orbaallegro")->__("Items sold"),
            "width"     => "100px"
        ));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn("stock_qty", array(
                "index"     => "stock_qty",
                "type"      => "number",
                "header"    => Mage::helper("orbaallegro")->__("Stock Qty"),
                "align"     => "right",
                "width"     => "100px"
            ));
        }
        
        $this->addColumn("auction_item_price", array(
            "index"     => "auction_item_price",
            "type"      => "currency",
            "header"    => Mage::helper("orbaallegro")->__("Item price"),
            'currency'  => 'currency',
            "width"     => "100px"
        ));
        $this->addColumn("auction_status", array(
            "index"     =>"auction_status",
            "header"    => Mage::helper("orbaallegro")->__("Status"),
            "width"     => "100px",
            "type"      => "options",
            "options"   => Mage::getSingleton("orbaallegro/auction_status")->toOptionHash()
        ));
        $this->addColumn("auction_additional_options", array(
            "index"     =>"auction_additional_options",
            "header"    => Mage::helper("orbaallegro")->__("Additional options"),
            "width"     => "100px",
            "type"      => "options",
            "options"   => Mage::getSingleton("orbaallegro/system_config_source_attribute_template_additionaloption")->
                toOptionHash(),
            "renderer"  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_auction_grid_column_renderer_additionaloptions"),
            'filter_condition_callback' => array(
                $this,
                '_filterAdditionalOptionsCondition'
            ) 
        ));
        $this->addColumn("end_time", array(
            "index"     => "end_time",
            "header"    =>  Mage::helper("orbaallegro")->__("Time to end"),
            "align"     => "right",
            "width"     => "100px",
            "type"      => "date",
            "renderer"  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_auction_grid_column_renderer_timetoend"),
            
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('orbaallegro')->__('Created from store'),
                'index'     => 'store_id',
                'type'      => 'store',
                "width"     => "150px",
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }
        $this->addColumn("country_code", array(
            "type"      => "options",
            "options"   => Mage::getSingleton('orbaallegro/system_config_source_service')->toOptionHash(),
            "header"    => Mage::helper("orbaallegro")->__("Service"),
            "width"     => "100px",
            "index"     => "country_code"
        ));
        $this->addColumn("view_online", array(
            "header"    => Mage::helper("orbaallegro")->__("View Online"),
            "sortable"  => false,
            "filter"    => false,
            "width"     => "100px",
            "renderer"  => Mage::getConfig()->
                getBlockClassName("orbaallegro/adminhtml_auction_grid_column_renderer_link")
        ));
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction() {
        $this->setMassactionIdField('auction_id');
        $this->getMassactionBlock()->setFormFieldName('auction_id');
        $this->getMassactionBlock()->addItem('finish_without_cancelling', array(
            'label'=> Mage::helper('orbaallegro')->__('Finish without cancelling bids'),
            'url'  => $this->getUrl('*/*/massFinish', array('cancel_bids' => 0)),
            'confirm' => Mage::helper('tax')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('finish_with_cancelling', array(
            'label'=> Mage::helper('orbaallegro')->__('Finish with cancelling bids'),
            'url'  => $this->getUrl('*/*/massFinish', array('cancel_bids' => 1)),
            'confirm' => Mage::helper('orbaallegro')->__('Are you sure?'),
            'additional' => array(
            	'cancel_reason' => array(
                    'name' => 'cancel_reason',
                    'type' => 'text',
                    'class' => 'required-entry',
                    'label' => Mage::helper('orbaallegro')->__('Cancel reason')
                )
            )
        ));
        $this->getMassactionBlock()->addItem('sell_again', array(
            'label'=> Mage::helper('orbaallegro')->__('Sell again'),
            'url'  => $this->getUrl('*/*/massSellAgain'),
            'additional' => array(
                'starting_time' => array(
                    'name' => 'starting_time',
                    'type' => 'date',
                    'label' => Mage::helper('orbaallegro')->__('Starting time'),
                    "image" => Mage::getDesign()->getSkinUrl("images/grid-cal.gif"),
                    "format" => $this->_getDateTimeFormat(),
                    "input_format" => $this->_getDateTimeFormat(),
                    "time" => true
                )
            )
        ));
        return $this;
    }
    
    protected function _getDateTimeFormat() {
        return Mage::app()->getLocale()
            ->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    protected function _filterAllegroCategoriesCondition($collection, $column) {
        $value = $column->getFilter()->getValue();
        if ($value && !empty($value)) {
            $resource = Mage::getSingleton('core/resource');
            $collection->getSelect()->joinLeft(
                array('category'=>$resource->getTableName('orbaallegro/category')),
                'main_table.category_id = category.category_id',
                array('category.name')
            );
            $collection->addFieldToFilter('category.name', array('like' => '%'.$value.'%'));
        }
    }
    
    protected function _filterAdditionalOptionsCondition($collection, $column) {
        /* @var $collection Mage_Core_Model_Resource_Db_Collection_Abstract */
        $filter = $column->getFilter();
        $value = $filter->getValue();
        
        if ($value && !empty($value)) {
           $value = (int)$value;
           // Bitwise operation
           $collection->addFilter(
                null,
                new Zend_Db_Expr("main_table.auction_additional_options & " . $value), 
                "string"
           );
        }
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/view', array('auction_id'=>$row->getId()));
    }
    
    public function getRowClass($item) {
        if($item->getStockQty()!==null){
            if($item->getStockQty()<$item->getItemsPlaced()){
                return "important";
            }
        }
        return null;
    }
    
}