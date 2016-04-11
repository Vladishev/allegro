<?php 

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

$toInsert = array(
    array(
        "country_code" => (int) Orba_Allegro_Model_Service::ID_ALLEGROPL,
        "external_id" => Orba_Allegro_Model_Service_Allegropl::ID_STARTING_TIME,
        "local_code" => Orba_Allegro_Model_Form_Auction::FIELD_STARTING_TIME,
        "sort_order" => 35
    ),
    array(
        "country_code" => (int) Orba_Allegro_Model_Service::ID_WEBAPI,
        "external_id" => Orba_Allegro_Model_Service_Allegropl::ID_STARTING_TIME,
        "local_code" => Orba_Allegro_Model_Form_Auction::FIELD_STARTING_TIME,
        "sort_order" => 35
    )
);

$connection->insertMultiple($installer->getTable("orbaallegro/mapping_sellform"), $toInsert);