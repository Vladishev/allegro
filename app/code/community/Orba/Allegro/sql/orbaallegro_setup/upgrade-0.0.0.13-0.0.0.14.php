<?php 

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

$auctionTable			= $installer->getTable('orbaallegro/auction');
$catalogProductTable	= $installer->getTable('catalog/product');

//Add Parent Product Id To Auction Table - Configurable Product Reference
$installer->getConnection()
	->addColumn($auctionTable, "product_parent_id", array(
		"type"		=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		"comment"	=> "Parent Product Id",
		"nullable"	=> true,	
		"default"	=> null,
		"after"		=> "product_id"
	));

$connection->addIndex($auctionTable, 
        $installer->getConnection()->getIndexName($auctionTable, array("product_parent_id")), 
        array("product_parent_id")
);

$connection->addForeignKey(
        $connection->getForeignKeyName($auctionTable, "product_parent_id", $catalogProductTable, 'entity_id'),
        $auctionTable, 
        "product_parent_id", 
        $catalogProductTable,
        "entity_id", 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, 
        Varien_Db_Ddl_Table::ACTION_CASCADE
);

//Add Buy Request Info: Super Attribute	
$installer->getConnection()
	->addColumn($auctionTable, "buy_request", array(
		"type"		=> Varien_Db_Ddl_Table::TYPE_TEXT,
		"comment"	=> "Buy Request: Super Attribute",
		"nullable"	=> true,	
		"default"	=> null
	));

$installer->endSetup();