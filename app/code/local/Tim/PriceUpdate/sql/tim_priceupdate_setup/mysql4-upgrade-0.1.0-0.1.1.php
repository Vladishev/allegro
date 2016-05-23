<?php
/**
 * Edit price update report table to replace SKU with Product ID
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->truncateTable($installer->getTable('tim_priceupdate/report'));
$installer->getConnection()->dropIndex(
    $installer->getTable('tim_priceupdate/report'),
    $installer->getIdxName('tim_priceupdate/report', array('sku'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);
$installer->getConnection()->dropColumn($installer->getTable('tim_priceupdate/report'), 'sku');
$installer->getConnection()->addColumn($installer->getTable('tim_priceupdate/report'), 'product_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => false,
    'comment'   => 'Product ID'
));
$installer->getConnection()->addIndex(
    $installer->getTable('tim_priceupdate/report'),
    $installer->getIdxName('tim_priceupdate/report', array('product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('product_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();