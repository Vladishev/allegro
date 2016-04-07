<?php 

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();

$auctionSerializedTable		= $installer->getTable('orbaallegro/auction_serialized');
$transactionSerializedTable = $installer->getTable('orbaallegro/transaction_serialized');
$allegroMappingTable		= $installer->getTable('orbaallegro/mapping');

$installer->getConnection()->modifyColumn($auctionSerializedTable, 'serialized_data', Varien_Db_Ddl_Table::TYPE_BLOB);
$installer->getConnection()->modifyColumn($transactionSerializedTable, 'serialized_data', Varien_Db_Ddl_Table::TYPE_BLOB);
$installer->getConnection()->modifyColumn($allegroMappingTable, 'conditions_serialized', Varien_Db_Ddl_Table::TYPE_BLOB);

$installer->endSetup();