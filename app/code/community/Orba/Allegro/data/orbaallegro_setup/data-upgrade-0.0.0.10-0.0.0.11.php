<?php 

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

$dataPayment = array(
    Orba_Allegro_Model_Service::ID_WEBAPI => array(
        array("tt", 0.50, 999999.99, 10, "Debit card", 1),
    ),
    Orba_Allegro_Model_Service::ID_ALLEGROPL => array(
        array("tt", 0.50, 999999.99, 10, "Debit card", 1),
    )
);

$toInsert = array();
$now = new Zend_Db_Expr("NOW()");

foreach ($dataPayment as $country_code => $payments) {
    foreach ($payments as $payment) {
        $toInsert[] = array(
            "country_code" => (int) $country_code,
            "allegro_payment_type" => $payment[0],
            "price_from" => $payment[1],
            "price_to" => $payment[2],
            "cancel_time" => $payment[3],
            "name" => $payment[4],
            "is_payu" => $payment[5],
            "created_at" => $now,
            "updated_at" => $now
        );
    }
}

$connection->insertMultiple($installer->getTable("orbaallegro/mapping_payment"), $toInsert);