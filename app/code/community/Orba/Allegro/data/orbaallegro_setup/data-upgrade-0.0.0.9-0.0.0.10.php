<?php 

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

$fieldsMapping = array(
    // Only for webapi
    Orba_Allegro_Model_Service::ID_WEBAPI => array(
		// Misc
        Orba_Allegro_Model_Service_Webapi::ID_TITLE => Orba_Allegro_Model_Form_Auction::FIELD_TITLE,
        Orba_Allegro_Model_Service_Webapi::ID_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION,
        Orba_Allegro_Model_Service_Webapi::ID_EXTENDED_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_EXTENDED_DESCRIPTION,
		Orba_Allegro_Model_Service_Webapi::ID_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_CATEGORY,
        Orba_Allegro_Model_Service_Webapi::ID_DURATION => Orba_Allegro_Model_Form_Auction::FIELD_DURATION,
        Orba_Allegro_Model_Service_Webapi::ID_QUANTITY => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY,
        Orba_Allegro_Model_Service_Webapi::ID_QUANTITY_TYPE => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY_TYPE,
        Orba_Allegro_Model_Service_Webapi::ID_ADDITIONAL_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_ADDITIONAL_OPTIONS,
        Orba_Allegro_Model_Service_Webapi::ID_BUY_NOW_PRICE => Orba_Allegro_Model_Form_Auction::FIELD_BUY_NOW_PRICE,
		// Location
        Orba_Allegro_Model_Service_Webapi::ID_COUNTRY => Orba_Allegro_Model_Form_Auction::FIELD_COUNTRY,
		Orba_Allegro_Model_Service_Webapi::ID_POSTCODE => Orba_Allegro_Model_Form_Auction::FIELD_POSTCODE,
        Orba_Allegro_Model_Service_Webapi::ID_CITY => Orba_Allegro_Model_Form_Auction::FIELD_CITY,
        Orba_Allegro_Model_Service_Webapi::ID_PROVINCE => Orba_Allegro_Model_Form_Auction::FIELD_PROVINCE,
        // Img
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_1 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_1,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_2 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_2,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_3 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_3,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_4 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_4,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_5 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_5,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_6 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_6,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_7 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_7,
        Orba_Allegro_Model_Service_Webapi::ID_IMAGE_8 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_8,
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
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_BUSINESS_PARCEL,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_BUSINESS_PARCEL_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_EPRZESYLKA,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_RUCH,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_PACZKOMATY,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_DHL,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_RUCH,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY
    ),
    Orba_Allegro_Model_Service::ID_ALLEGROPL => array(
        // Misc
        Orba_Allegro_Model_Service_Allegropl::ID_TITLE => Orba_Allegro_Model_Form_Auction::FIELD_TITLE,
        Orba_Allegro_Model_Service_Allegropl::ID_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION,
        Orba_Allegro_Model_Service_Allegropl::ID_EXTENDED_DESCRIPTION => Orba_Allegro_Model_Form_Auction::FIELD_EXTENDED_DESCRIPTION,
		Orba_Allegro_Model_Service_Allegropl::ID_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_CATEGORY,
        Orba_Allegro_Model_Service_Allegropl::ID_SHOP_CATEGORY => Orba_Allegro_Model_Form_Auction::FIELD_SHOP_CATEGORY,
        Orba_Allegro_Model_Service_Allegropl::ID_DURATION => Orba_Allegro_Model_Form_Auction::FIELD_DURATION,
        Orba_Allegro_Model_Service_Allegropl::ID_QUANTITY => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY,
        Orba_Allegro_Model_Service_Allegropl::ID_QUANTITY_TYPE => Orba_Allegro_Model_Form_Auction::FIELD_QUANTITY_TYPE,
        Orba_Allegro_Model_Service_Allegropl::ID_SALES_FORMAT => Orba_Allegro_Model_Form_Auction::FIELD_SALES_FORMAT,
        Orba_Allegro_Model_Service_Allegropl::ID_AUTO_RENEW => Orba_Allegro_Model_Form_Auction::FIELD_AUTO_RENEW,
        Orba_Allegro_Model_Service_Allegropl::ID_ADDITIONAL_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_ADDITIONAL_OPTIONS,
        Orba_Allegro_Model_Service_Allegropl::ID_ADDITIONAL_OPTIONS => Orba_Allegro_Model_Form_Auction::FIELD_ADDITIONAL_OPTIONS,
        Orba_Allegro_Model_Service_Allegropl::ID_BUY_NOW_PRICE => Orba_Allegro_Model_Form_Auction::FIELD_BUY_NOW_PRICE,
		// Location
        Orba_Allegro_Model_Service_Allegropl::ID_COUNTRY => Orba_Allegro_Model_Form_Auction::FIELD_COUNTRY,
		Orba_Allegro_Model_Service_Allegropl::ID_POSTCODE => Orba_Allegro_Model_Form_Auction::FIELD_POSTCODE,
        Orba_Allegro_Model_Service_Allegropl::ID_CITY => Orba_Allegro_Model_Form_Auction::FIELD_CITY,
        Orba_Allegro_Model_Service_Allegropl::ID_PROVINCE => Orba_Allegro_Model_Form_Auction::FIELD_PROVINCE,
		// Img
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_1 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_1,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_2 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_2,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_3 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_3,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_4 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_4,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_5 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_5,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_6 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_6,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_7 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_7,
        Orba_Allegro_Model_Service_Allegropl::ID_IMAGE_8 => Orba_Allegro_Model_Form_Auction::FIELD_IMAGE_8,
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
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY,
        
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_COURIER_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_COURIER_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_COURIER_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_COURIER_PARCEL_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_BUSINESS_PARCEL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_BUSINESS_PARCEL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_BUSINESS_PARCEL,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_BUSINESS_PARCEL_COD,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_BUSINESS_PARCEL_COD => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_BUSINESS_PARCEL_COD,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_EPRZESYLKA,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_RUCH,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_PACZKOMATY,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_DHL,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_DHL => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_DHL,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_RUCH,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_RUCH => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_RUCH,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PACZKA24,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PACZKA24,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PACZKA24,
		
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PACZKA48 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PACZKA48,
		Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PACZKA48 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PACZKA48,
		Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PACZKA48 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PACZKA48,
		
        Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24,
        
		Orba_Allegro_Model_Service_Allegropl::ID_FIRST_ITEM_PICKPOINT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_FIRST_ITEM_PICKPOINT_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_NEXT_ITEM_PICKPOINT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_NEXT_ITEM_PICKPOINT_PACZKA24,
        Orba_Allegro_Model_Service_Allegropl::ID_ITEM_COUNT_PICKPOINT_PACZKA24 => Orba_Allegro_Model_Form_Auction::FIELD_ITEM_COUNT_PICKPOINT_PACZKA24
    )
);

$sellformFieldIds = Mage::getModel('orbaallegro/mapping_sellform')->getFieldIds();

$toInsert = array();
foreach ($fieldsMapping as $country_code => $fields) {
    $sort = 0;
    foreach ($fields as $externalId => $localCode) {
        $record = array(
            "country_code" => (int) $country_code,
            "external_id" => $externalId,
            "local_code" => $localCode,
            "sort_order" => $sort * 10
        );
		if (isset($sellformFieldIds[$country_code]) && isset($sellformFieldIds[$country_code][$externalId])) {
			$record["field_id"] = $sellformFieldIds[$country_code][$externalId];
		}
		$connection->insertOnDuplicate($installer->getTable("orbaallegro/mapping_sellform"), $record, array('sort_order'));
        $sort++;
    }
}