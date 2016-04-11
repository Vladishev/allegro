<?php
$installer = $this;
$installer->startSetup();

$installer->run("UPDATE {$this->getTable('sales_flat_quote')} SET `is_active` = '0' WHERE `is_active` = 1 AND `orbaallegro_transaction_id` IS NOT NULL");

$installer->endSetup();