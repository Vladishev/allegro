<?php

/* v.0.0.0.6 - Implement re-new ended auction */

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();

$auctionTable = $installer->getTable('orbaallegro/auction');
// Add do_again_type - small int		
$installer->getConnection()->addColumn($auctionTable, "do_renew", array(
	"type" => Varien_Db_Ddl_Table::TYPE_SMALLINT,
	"comment" => "Do sell agian?",
	"default" => 0
));
// Add sell_again_type - small int		
$installer->getConnection()->addColumn($auctionTable, "renew_type", array(
	"type" => Varien_Db_Ddl_Table::TYPE_SMALLINT,
	"comment" => "Sell agian type?",
	"default" => 0
));
// Add sell_again_items - int
$installer->getConnection()->addColumn($auctionTable, "renew_items", array(
	"type" => Varien_Db_Ddl_Table::TYPE_INTEGER,
	"comment" => "Sell agian items?",
	"nullable" => true
));
$installer->endSetup();