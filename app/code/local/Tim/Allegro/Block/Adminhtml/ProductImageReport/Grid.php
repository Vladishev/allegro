<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Block_Adminhtml_ProductImageReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productImageReportGrid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('status');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToFilter(
            'status',
            array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
        );
        $collection->addAttributeToFilter(array(
            array (
                'attribute' => 'image',
                'like' => 'no_selection'
            ),
            array (
                'attribute' => 'image', // null fields
                'null' => true
            ),
            array (
                'attribute' => 'image', // empty, but not null
                'eq' => ''
            ),
            array (
                'attribute' => 'image', // check for information that doesn't conform to Magento's formatting
                'nlike' => '%/%/%'
            ),
        ));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Tim_Allegro_Block_Adminhtml_ProductImage_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_allegro')->__('Name of product'),
            'width' => '100',
            'index' => 'name',
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

    /**
     * Returns a row URL
     *
     * @param mixed $row row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array(
                'store' => $this->getRequest()->getParam('store'),
                'id' => $row->getId())
        );
    }
}