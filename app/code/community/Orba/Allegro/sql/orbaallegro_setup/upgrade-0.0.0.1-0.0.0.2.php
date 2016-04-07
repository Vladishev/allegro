<?php

/* v.0.0.0.2 */

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

/**
 * Add contractor entity
 */
$contractorTable = $installer->getTable('orbaallegro/contractor');

$table = $connection
    ->newTable($contractorTable)
    ->addColumn("contractor_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    // Account Data
    ->addColumn("allegro_user_id",  Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false))
    ->addColumn('country_code',     Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    ->addColumn('login',            Varien_Db_Ddl_Table::TYPE_TEXT, 100, array('nullable'  => false))
    ->addColumn('email',            Varien_Db_Ddl_Table::TYPE_TEXT, 150, array('nullable'  => false))
    ->addColumn('firstname',        Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('lastname',         Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('company',          Varien_Db_Ddl_Table::TYPE_TEXT, 150, array())
    ->addColumn('postcode',         Varien_Db_Ddl_Table::TYPE_TEXT, 20, array())
    ->addColumn('street',           Varien_Db_Ddl_Table::TYPE_TEXT, 150, array())
    ->addColumn('city',             Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('phone',            Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('country',          Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    ->addColumn('region',           Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    // Send Data
    ->addColumn('has_send_data',    Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('default'=>0, 'nullable' => false))
    ->addColumn('send_firstname',   Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('send_lastname',    Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('send_company',     Varien_Db_Ddl_Table::TYPE_TEXT, 150, array())
    ->addColumn('send_postcode',    Varien_Db_Ddl_Table::TYPE_TEXT, 20, array())
    ->addColumn('send_street',      Varien_Db_Ddl_Table::TYPE_TEXT, 150, array())
    ->addColumn('send_city',        Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('send_country',     Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    // Company Data
    ->addColumn('has_company_data', Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('default'=>0, 'nullable' => false))
    ->addColumn('company_firstname',Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('company_lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('company_company',  Varien_Db_Ddl_Table::TYPE_TEXT, 150, array())
    ->addColumn('company_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array())
    ->addColumn('company_street',   Varien_Db_Ddl_Table::TYPE_TEXT, 150, array())
    ->addColumn('company_city',     Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())
    ->addColumn('company_country',  Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    // Misc
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex($installer->getIdxName('orbaallegro/contractor', array('allegro_user_id')),
        array('allegro_user_id'))
    ->addIndex($installer->getIdxName('orbaallegro/contractor', array('country_code')),
        array('country_code'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/contractor', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code');
$installer->getConnection()->createTable($table);

/**
 * Add contractor ID to transaction entity
 */
       
$transacetionTable = $installer->getTable('orbaallegro/transaction');

$connection->addColumn($transacetionTable, "contractor_id", array(
    "type" => Varien_Db_Ddl_Table::TYPE_INTEGER,
    "nullable" => true,
    "comment" => "Contractor id"
));

$connection->addIndex($transacetionTable, 
        $installer->getConnection()->getIndexName($transacetionTable, array("contractor_id")), 
        array("contractor_id")
);

$connection->addForeignKey(
        $connection->getForeignKeyName($transacetionTable, "contractor_id", $contractorTable, "contractor_id"),
        $transacetionTable, 
        "contractor_id", 
        $contractorTable, 
        "contractor_id", 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, 
        Varien_Db_Ddl_Table::ACTION_CASCADE
);



$installer->endSetup();