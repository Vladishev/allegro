<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;
$connection = $installer->getConnection();

$importCsvTable = $installer->getTable('tim_allegro/import');
$installer->startSetup();

if (!$connection->isTableExists($importCsvTable)) {
    $table = $connection->newTable($importCsvTable)
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'CSV id')
        ->addColumn('file_name', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'File name')
        ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true,
            'default' => null,
        ), 'Created date')
        ->addColumn('executed_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true,
            'default' => null,
        ), 'Executed date')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true,
        ), 'Status')
        ->addColumn('last_sku', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Last sku');

    $connection->createTable($table);
}

$installer->endSetup();