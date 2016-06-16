<?php
/**
 * A grid for price a update report
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_priceupdate_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare report collection
     *
     * @return Tim_PriceUpdate_Block_Adminhtml_Report_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $reportCollection Tim_PriceUpdate_Model_Resource_Report_Collection */
        $reportCollection = Mage::getModel('tim_priceupdate/report')->getCollection();
        $reportCollection->addFieldToSelect(array(
            'main_table.entity_id' => 'entity_id',
            'product_id',
            'price_old',
            'price_new',
            'price_diff',
            'registered_price_old',
            'registered_price_new',
            'registered_price_diff',
            'manufacturer_price_old',
            'manufacturer_price_new',
            'manufacturer_price_diff',
            'updated_at',
            'main_table.updated_at' => 'updated_at',
        ));

        // join product table
        $reportCollection->getSelect()->joinLeft(
            array('product_table' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
            "main_table.product_id = product_table.entity_id",
            array('sku')
        );

        // join product name
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');
        $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
        $reportCollection->getSelect()->joinLeft(
            array('name_table' => $attribute->getBackendTable()),
            "product_table.entity_id = name_table.entity_id AND name_table.attribute_id = {$attribute->getId()} AND name_table.store_id = {$storeId}",
            array('name_table.value' => 'value')
        );

        $this->setCollection($reportCollection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Tim_PriceUpdate_Block_Adminhtml_Report_Grid
     */
    protected function _prepareColumns()
    {
        /* @var $helper Tim_PriceUpdate_Helper_Data */
        $helper = Mage::helper('tim_priceupdate');
        $store = Mage::app()->getStore();

        $this->addColumn('entity_id', array(
            'header' => $helper->__('ID'),
            'index'  => 'main_table.entity_id',
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Product Name'),
            'index'  => 'name_table.value',
        ));

        $this->addColumn('sku', array(
            'header' => $helper->__('Product SKU'),
            'index'  => 'sku',
        ));

        $this->addColumn('price_old', array(
            'header'        => $helper->__('Price Old'),
            'type'          => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index'         => 'price_old',
        ));

        $this->addColumn('price_new', array(
            'header'        => $helper->__('Price New'),
            'type'          => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index'         => 'price_new',
        ));

        $this->addColumn('price_diff', array(
            'header' => $helper->__('Price Difference %'),
            'renderer' => 'tim_priceupdate/adminhtml_widget_grid_column_renderer_percentage',
            'index'  => 'price_diff',
        ));

        $this->addColumn('registered_price_old', array(
            'header'        => $helper->__('Registered Price Old'),
            'type'          => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index'         => 'registered_price_old',
        ));

        $this->addColumn('registered_price_new', array(
            'header'        => $helper->__('Registered Price New'),
            'type'          => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index'         => 'registered_price_new',
        ));

        $this->addColumn('registered_price_diff', array(
            'header' => $helper->__('Registered Price Difference %'),
            'renderer' => 'tim_priceupdate/adminhtml_widget_grid_column_renderer_percentage',
            'index'  => 'registered_price_diff',
        ));

        $this->addColumn('manufacturer_price_old', array(
            'header'        => $helper->__('Manufacturer Price Old'),
            'type'          => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index'         => 'manufacturer_price_old',
        ));

        $this->addColumn('manufacturer_price_new', array(
            'header'        => $helper->__('Manufacturer Price New'),
            'type'          => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index'         => 'manufacturer_price_new',
        ));

        $this->addColumn('manufacturer_price_diff', array(
            'header' => $helper->__('Manufacturer Price Difference %'),
            'renderer' => 'tim_priceupdate/adminhtml_widget_grid_column_renderer_percentage',
            'index'  => 'manufacturer_price_diff',
        ));

        $this->addColumn('updated_at', array(
            'header' => $helper->__('Modification Date'),
            'type'   => 'datetime',
            'index'  => 'main_table.updated_at',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Returns a grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}