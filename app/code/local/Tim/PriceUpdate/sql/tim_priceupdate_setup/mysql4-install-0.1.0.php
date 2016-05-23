<?php
/**
 * Create a table in DB to store a price update report
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$priceUpdateReportTable = $installer->getConnection()->newTable($installer->getTable('tim_priceupdate/report'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Entity ID'
    )
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Product SKU')
    ->addColumn('price_old', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Product Old Price')
    ->addColumn('price_new', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Product New Price')
    ->addColumn('price_diff', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), '% of difference between old and new price')
    ->addColumn('registered_price_old', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Product Old Registered Price')
    ->addColumn('registered_price_new', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Product New Registered Price')
    ->addColumn('registered_price_diff', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), '% of difference between old and new registered price')
    ->addColumn('manufacturer_price_old', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Product Old Manufacturer Price')
    ->addColumn('manufacturer_price_new', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Product New Manufacturer Price')
    ->addColumn('manufacturer_price_diff', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), '% of difference between old and new manufacturer price')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Modification Date')
    ->addIndex($installer->getIdxName('tim_priceupdate/report', array('sku'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('sku'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$installer->getConnection()->createTable($priceUpdateReportTable);

$installer->endSetup();