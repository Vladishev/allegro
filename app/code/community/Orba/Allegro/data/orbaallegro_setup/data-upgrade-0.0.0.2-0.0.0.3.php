<?php 

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */


$fieldsMapping = array(
    Orba_Allegro_Model_Service::ID_WEBAPI => array(
    ),
    Orba_Allegro_Model_Service::ID_ALLEGROPL => array(
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PACZKA48 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PACZKA48,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_PACZKA24,
        
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PACZKA48 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PACZKA48,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_PACZKA24,
        
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PACZKA48 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PACZKA48,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_PACZKA24,
    )
);

$toInsert = array();
foreach ($fieldsMapping as $country_code => $fields) {
    $sort = 0;
    foreach ($fields as $externalId => $localCode) {
        $toInsert[] = array(
            "country_code" => (int) $country_code,
            "external_id" => $externalId,
            "local_code" => $localCode,
            "sort_order" => $sort * 10
        );
        $sort++;
    }
}

$connection->insertMultiple($installer->getTable("orbaallegro/mapping_sellform"), $toInsert);


?>
