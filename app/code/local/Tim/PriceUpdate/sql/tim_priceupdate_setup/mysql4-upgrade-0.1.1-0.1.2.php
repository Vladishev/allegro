<?php
/**
 * Edit price update report table to store a percentage as a float value
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
$installer->getConnection()->modifyColumn($installer->getTable('tim_priceupdate/report'), 'price_diff', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'  => '12,4',
        'comment' => '% of difference between old and new price',
));

$installer->getConnection()->modifyColumn($installer->getTable('tim_priceupdate/report'), 'registered_price_diff', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'  => '12,4',
        'comment' => '% of difference between old and new registered price',
));

$installer->getConnection()->modifyColumn($installer->getTable('tim_priceupdate/report'), 'manufacturer_price_diff', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'  => '12,4',
        'comment' => '% of difference between old and new manufacturer price',
));

$installer->endSetup();