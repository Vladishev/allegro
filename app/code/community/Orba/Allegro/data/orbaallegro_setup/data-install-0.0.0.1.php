<?php

/* Get tamplate skin from $_SERVER */
$ORBAALLEGRO_TEMPLATE_DIRECTORY = isset($_SERVER["ORBAALLEGRO_TEMPLATE_DIRECTORY"]) ? 
        $_SERVER["ORBAALLEGRO_TEMPLATE_DIRECTORY"] : "default";

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

/******************************************************************************
 *
 * Install default sevices & attribute sets
 * 
 ******************************************************************************/

$installer->installDefualtServices();

/******************************************************************************
 *
 * Install payments values provided by PayU
 * 
 ******************************************************************************/
$dataPayment = array(
    Orba_Allegro_Model_Service::ID_WEBAPI => array(
        array("m", 0.50, 999999.99, 10, "mTransfer-mBank", 1),
        array("n", 0.50, 999999.99, 10, "MultiTransfer - MultiBank", 1),
        array("w", 0.50, 7000.003, 10, "BZWBK - Przelew24", 1),
        array("o", 0.50, 999999.99, 10, "Pekao24Przelew - Bank Pekao", 1),
        array("i", 0.50, 999999.99, 10, "Pay with Inteligo", 1),
        array("d", 0.50, 999999.99, 10, "Pay with Nordea", 1),
        array("p", 0.50, 999999.99, 10, "Pay with iPKO", 1),
        array("h", 0.50, 999999.99, 10, "Pay with z BPH", 1),
        array("g", 0.50, 999999.99, 10, "Pay with z ING", 1),
        array("l", 0.50, 999999.99, 10, "Credit Agricole", 1),
        array("as", 0.50, 999999.99, 10, "Pay with Alior Sync", 1),
        array("u", 0.50, 999999.99, 10, "Eurobank", 1),
        array("me", 0.50, 999999.99, 10, "Meritum Bank", 1),
        array("ab", 0.50, 999999.99, 10, "Pay with Alior Bankiem", 1),
        array("wp", 0.50, 999999.99, 10, "Transfer from Polbank", 1),
        array("wm", 0.50, 999999.99, 10, "Transfer from Millennium", 1),
        array("wk", 0.50, 999999.99, 10, "Transfer from Kredyt Bank", 1),
        array("wg", 0.50, 999999.99, 10, "Transfer from BGŻ", 1),
        array("wd", 0.50, 999999.99, 10, "Transfer from Deutsche Bank", 1),
        array("wr", 0.50, 999999.99, 10, "Transfer from Raiffeisen Bank", 1),
        array("wc", 0.50, 999999.99, 10, "Transfer from Citibank", 1),
        array("wn", 0.50, 999999.99, 10, "Transfer from Invest Bank", 1),
        array("wi", 0.50, 999999.99, 10, "Transfer from Getin Bank", 1),
        array("wy", 0.50, 999999.99, 10, "Transfer from Bank Pocztowy", 1),
        array("c", 1.01, 7000.004, 5, "Credit card", 1),
        array("b", 0.50, 999999.99, 10, "Bank transfer", 1),
        array("t", 0.50, 1000.00, 1, "Test payment", 1),
        // No pay u 
        array("collect_on_delivery", null, null, null, "Cash on delivery", 0),
        array("wire_transfer", null, null, null, "Oridinar bank transfer - out of PayU", 0),
        array("not_specified", null, null, null, "Not specified", 0),
    ),
    Orba_Allegro_Model_Service::ID_ALLEGROPL => array(
        array("m", 0.50, 999999.99, 10, "mTransfer-mBank", 1),
        array("n", 0.50, 999999.99, 10, "MultiTransfer - MultiBank", 1),
        array("w", 0.50, 7000.003, 10, "BZWBK - Przelew24", 1),
        array("o", 0.50, 999999.99, 10, "Pekao24Przelew - Bank Pekao", 1),
        array("i", 0.50, 999999.99, 10, "Pay with Inteligo", 1),
        array("d", 0.50, 999999.99, 10, "Pay with Nordea", 1),
        array("p", 0.50, 999999.99, 10, "Pay with iPKO", 1),
        array("h", 0.50, 999999.99, 10, "Pay with z BPH", 1),
        array("g", 0.50, 999999.99, 10, "Pay with z ING", 1),
        array("l", 0.50, 999999.99, 10, "Credit Agricole", 1),
        array("as", 0.50, 999999.99, 10, "Pay with Alior Sync", 1),
        array("u", 0.50, 999999.99, 10, "Eurobank", 1),
        array("me", 0.50, 999999.99, 10, "Meritum Bank", 1),
        array("ab", 0.50, 999999.99, 10, "Pay with Alior Bankiem", 1),
        array("wp", 0.50, 999999.99, 10, "Transfer from Polbank", 1),
        array("wm", 0.50, 999999.99, 10, "Transfer from Millennium", 1),
        array("wk", 0.50, 999999.99, 10, "Transfer from Kredyt Bank", 1),
        array("wg", 0.50, 999999.99, 10, "Transfer from BGŻ", 1),
        array("wd", 0.50, 999999.99, 10, "Transfer from Deutsche Bank", 1),
        array("wr", 0.50, 999999.99, 10, "Transfer from Raiffeisen Bank", 1),
        array("wc", 0.50, 999999.99, 10, "Transfer from Citibank", 1),
        array("wn", 0.50, 999999.99, 10, "Transfer from Invest Bank", 1),
        array("wi", 0.50, 999999.99, 10, "Transfer from Getin Bank", 1),
        array("wy", 0.50, 999999.99, 10, "Transfer from Bank Pocztowy", 1),
        array("c", 1.01, 7000.004, 5, "Credit card", 1),
        array("b", 0.50, 999999.99, 10, "Bank transfer", 1),
        array("t", 0.50, 1000.00, 1, "Test payment", 1),
        // No pay u 
        array("collect_on_delivery", null, null, null, "Cash on delivery", 0),
        array("wire_transfer", null, null, null, "Oridinar bank transfer - out of PayU", 0),
        array("not_specified", null, null, null, "Not specified", 0),
    ),
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

/******************************************************************************
 *
 * Install form field mappings
 * 
 ******************************************************************************/

$fieldsMapping = array(
    // Only for webapi
    Orba_Allegro_Model_Service::ID_WEBAPI => array(
        // Location
        Orba_Allegro_Model_Service_Webapi::ID_COUNTRY => Orba_Allegro_Model_Form_Auction::FIELD_COUNTRY,
        Orba_Allegro_Model_Service_Webapi::ID_CITY => Orba_Allegro_Model_Form_Auction::FIELD_CITY,
        Orba_Allegro_Model_Service_Webapi::ID_PROVINCE => Orba_Allegro_Model_Form_Auction::FIELD_PROVINCE,
        Orba_Allegro_Model_Service_Webapi::ID_POSTCODE => Orba_Allegro_Model_Form_Auction::FIELD_POSTCODE,
        // Img
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_1 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_1,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_2 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_2,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_3 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_3,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_4 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_4,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_5 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_5,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_6 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_6,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_7 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_7,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_8 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_8,
        // Misc
        Orba_Allegro_Model_Service_Webapi::ID_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_CATEGORY,
        Orba_Allegro_Model_Service_Webapi::ID_TITLE => Orba_Allegro_Model_Form_Auction::FIELD_TITLE,
        Orba_Allegro_Model_Service_Webapi::ID_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION,
        Orba_Allegro_Model_Service_Webapi::ID_EXTENDED_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_EXTENDED_DESCRIPTION,
        Orba_Allegro_Model_Service_Webapi::ID_DURATION => Orba_Allegro_Model_Form_Auction::FIELD_DURATION,
        Orba_Allegro_Model_Service_Webapi::ID_QUANTITY => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY,
        Orba_Allegro_Model_Service_Webapi::ID_QUANTITY_TYPE => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY_TYPE,
        Orba_Allegro_Model_Service_Webapi::ID_ADDITIONAL_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_ADDITIONAL_OPTIONS,
        Orba_Allegro_Model_Service_Webapi::ID_BUY_NOW_PRICE => Orba_Allegro_Model_Form_Auction::FIELD_BUY_NOW_PRICE,
        // Payment
        Orba_Allegro_Model_Service_Webapi::ID_PAYMENT_TYPES => Orba_Allegro_Model_Form_Auction::FIELD_PAYMENT_TYPES,
        Orba_Allegro_Model_Service_Webapi::ID_PAYMENT_BANK_ACCOUNT_1 => Orba_Allegro_Model_Form_Auction::FIELD_BANK_ACCOUNT_1,
        Orba_Allegro_Model_Service_Webapi::ID_PAYMENT_BANK_ACCOUNT_2 => Orba_Allegro_Model_Form_Auction::FIELD_BANK_ACCOUNT_2,
        // Shipping
        Orba_Allegro_Model_Service_Webapi::ID_SHIPPING_INFO => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_INFO,
        Orba_Allegro_Model_Service_Webapi::ID_FREE_SHIPPING => Orba_Allegro_Model_Form_Auction::FIELD_FREE_SHIPPING,
        Orba_Allegro_Model_Service_Webapi::ID_SHIPPING_PAYER => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_PAYER,
        Orba_Allegro_Model_Service_Webapi::ID_SHIPPING_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_OPTIONS,
        // Factored
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Webapi::ID_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Webapi::ID_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Webapi::ID_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Webapi::ID_SHOP_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_SHOP_CATEGORY
    ),
    Orba_Allegro_Model_Service::ID_ALLEGROPL => array(
        // Location
        Orba_Allegro_Model_Service_Allegropl::ID_COUNTRY => Orba_Allegro_Model_Form_Auction::FIELD_COUNTRY,
        Orba_Allegro_Model_Service_Allegropl::ID_CITY => Orba_Allegro_Model_Form_Auction::FIELD_CITY,
        Orba_Allegro_Model_Service_Allegropl::ID_PROVINCE => Orba_Allegro_Model_Form_Auction::FIELD_PROVINCE,
        Orba_Allegro_Model_Service_Allegropl::ID_POSTCODE => Orba_Allegro_Model_Form_Auction::FIELD_POSTCODE,
        // Img
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_1 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_1,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_2 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_2,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_3 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_3,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_4 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_4,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_5 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_5,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_6 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_6,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_7 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_7,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_8 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_8,
        // Misc
        Orba_Allegro_Model_Service_Allegropl::ID_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_CATEGORY,
        Orba_Allegro_Model_Service_Allegropl::ID_SHOP_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_SHOP_CATEGORY,
        Orba_Allegro_Model_Service_Allegropl::ID_TITLE => Orba_Allegro_Model_Form_Auction::FIELD_TITLE,
        Orba_Allegro_Model_Service_Allegropl::ID_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION,
        Orba_Allegro_Model_Service_Allegropl::ID_EXTENDED_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_EXTENDED_DESCRIPTION,
        Orba_Allegro_Model_Service_Allegropl::ID_DURATION => Orba_Allegro_Model_Form_Auction::FIELD_DURATION,
        Orba_Allegro_Model_Service_Allegropl::ID_QUANTITY => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY,
        Orba_Allegro_Model_Service_Allegropl::ID_QUANTITY_TYPE => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY_TYPE,
        Orba_Allegro_Model_Service_Allegropl::ID_SALES_FORMAT => Orba_Allegro_Model_Form_Auction::FIELD_SALES_FORMAT,
        Orba_Allegro_Model_Service_Allegropl::ID_AUTO_RENEW => Orba_Allegro_Model_Form_Auction::FIELD_AUTO_RENEW,
        Orba_Allegro_Model_Service_Allegropl::ID_ADDITIONAL_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_ADDITIONAL_OPTIONS,
        Orba_Allegro_Model_Service_Allegropl::ID_ADDITIONAL_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_ADDITIONAL_OPTIONS,
        Orba_Allegro_Model_Service_Allegropl::ID_BUY_NOW_PRICE => Orba_Allegro_Model_Form_Auction::FIELD_BUY_NOW_PRICE,
        // Payment
        Orba_Allegro_Model_Service_Allegropl::ID_PAYMENT_TYPES => Orba_Allegro_Model_Form_Auction::FIELD_PAYMENT_TYPES,
        Orba_Allegro_Model_Service_Allegropl::ID_PAYMENT_BANK_ACCOUNT_1 => Orba_Allegro_Model_Form_Auction::FIELD_BANK_ACCOUNT_1,
        Orba_Allegro_Model_Service_Allegropl::ID_PAYMENT_BANK_ACCOUNT_2 => Orba_Allegro_Model_Form_Auction::FIELD_BANK_ACCOUNT_2,
        // Shipping
        Orba_Allegro_Model_Service_Allegropl::ID_SHIPPING_INFO => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_INFO,
        Orba_Allegro_Model_Service_Allegropl::ID_FREE_SHIPPING => Orba_Allegro_Model_Form_Auction::FIELD_FREE_SHIPPING,
        Orba_Allegro_Model_Service_Allegropl::ID_SHIPPING_PAYER => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_PAYER,
        Orba_Allegro_Model_Service_Allegropl::ID_SHIPPING_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_OPTIONS,
        Orba_Allegro_Model_Service_Allegropl::ID_SHIPPING_TIME => Orba_Allegro_Model_Form_Auction::FIELD_SHIPPING_TIME,
        // Factored
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_SHOP_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_SHOP_CATEGORY
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

/******************************************************************************
 *
 * Install (basic) Template form attributes
 * @todo: install another paramters
 * 
 ******************************************************************************/

foreach ($fieldsMapping as $country_code => $fields) {
    foreach ($fields as $externalId => $localCode) {
        $attrData = array(
            "allegro_form_id"=>$externalId
        );
        switch ($localCode) {
            case Orba_Allegro_Model_Form_Auction::FIELD_TITLE:
                 $attrData = array_merge($attrData, array(
                    "type"              => "varchar",
                    "input"             => "text",
                    "label"             => "Title",
                    "position"          => 30,
                    "global"            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    "required"          => 0,
                    "group"             => Orba_Allegro_Model_Template::GROUP_GENERAL
                ));
            break;
            // Description (HTML)
            case Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION:
                 $attrData = array_merge($attrData, array(
                    "type"              => "text",
                    "input"             => "textarea",
                    "label"             => "Description (HTML)",
                    "is_wysiwyg_enabled"=> 1,
                    "position"          => 40,
                    "global"            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    "required"          => 0,
                    "group"             => Orba_Allegro_Model_Template::GROUP_GENERAL
                ));
            break;
        }
        if(count($attrData)>1){
            $installer->addAttribute(Orba_Allegro_Model_Template::ENTITY, $localCode, $attrData);
        }
    }
}

/******************************************************************************
 *
 * Install Default CMS Static Blocks
 * 
 ******************************************************************************/

$baseDir = __DIR__ . DS . "contents" . DS . $ORBAALLEGRO_TEMPLATE_DIRECTORY;
$cmsDir = $baseDir . DS . "cms";

foreach(glob($cmsDir.DS."*") as $file){
    $basename = basename($file, ".phtml");
    $contents = file_get_contents($file);

    $model = Mage::getModel("cms/block");
    /* @var $model Mage_Cms_Model_Block */
    $model->setTitle("ORBA | Allegro: " . $basename);
    $model->setContent($contents);
    $model->setIdentifier("orbaallegro_" . $basename);
    $model->setStores(Mage_Core_Model_App::ADMIN_STORE_ID);
    $model->getIsActive(1);
    $model->save();
}

/******************************************************************************
 *
 * Install Default Template
 * 
 ******************************************************************************/

$templateDir = $baseDir . DS . "template";
$codesToIds = Mage::getSingleton("orbaallegro/service")->getConstCodesToIds();
$codesToLabels = Mage::getSingleton("orbaallegro/service")->getConstCountryCodesToLabel();


foreach(glob($templateDir . DS . "*") as $file){
    $basename = basename($file, ".phtml");
    if(array_key_exists($basename, $codesToIds)){
        $contents = file_get_contents($file);
        $countryCode = $codesToIds[$basename];
        $label = $codesToLabels[$countryCode];
        $model = Mage::getModel("orbaallegro/template");
        /* @var $model Orba_Allegro_Model_Template */
        $model->setName(Mage::helper("orbaallegro")->__("Default") . " " . $label);
        $model->setCountryCode($countryCode);
        $model->setTitle("{{var product.name}}");
        $model->setDescription($contents);
        $model->save();
    }
    
}

/**
 * Set positive values to new attribute use global mapping
 */

Mage::getModel('catalog/product_action')->updateAttributes(
        Mage::getResourceModel("catalog/product_collection")->getAllIds(),
        array(Orba_Allegro_Model_Mapping::ATTR_CODE_USE_MAPPING=>1),
        Mage_Core_Model_App::ADMIN_STORE_ID
);

?>
