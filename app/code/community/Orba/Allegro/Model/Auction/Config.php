<?php
class Orba_Allegro_Model_Auction_Config extends Orba_Allegro_Model_Config {
    
    // Location pathes
    const XML_PATH_TEMPLATE_LOCALIZATION_COUNTRY     = 'orbaallegro/template_localization/country';
    const XML_PATH_TEMPLATE_LOCALIZATION_PROVINCE    = 'orbaallegro/template_localization/province';
    const XML_PATH_TEMPLATE_LOCALIZATION_CITY        = 'orbaallegro/template_localization/city';
    const XML_PATH_TEMPLATE_LOCALIZATION_POST_CODE   = 'orbaallegro/template_localization/post_code';
    
    // Images
    const XML_PATH_TEMPLATE_IMAGE_MAIN               = 'orbaallegro/template_image/main';
    const XML_PATH_TEMPLATE_IMAGE_COUNT              = 'orbaallegro/template_image/count';
    
    // Base pathes
    const XML_PATH_TEMPLATE_BASE_TITLE               = 'orbaallegro/template_base/title';
    const XML_PATH_TEMPLATE_BASE_DESCRIPTION         = 'orbaallegro/template_base/description';
    const XML_PATH_TEMPLATE_BASE_WEIGHT              = 'orbaallegro/template_base/weight';
    const XML_PATH_TEMPLATE_BASE_DURATION            = 'orbaallegro/template_base/duration';
    const XML_PATH_TEMPLATE_BASE_QUANTITY            = 'orbaallegro/template_base/quantity';
    const XML_PATH_TEMPLATE_BASE_QUANTITY_TYPE       = 'orbaallegro/template_base/quantity_type';
    const XML_PATH_TEMPLATE_BASE_BUY_NOW_PRICE       = 'orbaallegro/template_base/buy_now_price';
    const XML_PATH_TEMPLATE_BASE_ADDITIONAL_OPTIONS  = 'orbaallegro/template_base/additional_options';
    const XML_PATH_TEMPLATE_BASE_SALES_FORMAT        = 'orbaallegro/template_base/sales_format';
    const XML_PATH_TEMPLATE_BASE_AUTO_RENEW          = 'orbaallegro/template_base/auto_renew';
    // Payment
    const XML_PATH_TEMPLATE_PAYMENT_PAYMENT_TYPES    = 'orbaallegro/template_payment/payment_types';
    const XML_PATH_TEMPLATE_PAYMENT_BANK_ACCOUNT_1   = 'orbaallegro/template_payment/bank_account_1';
    const XML_PATH_TEMPLATE_PAYMENT_BANK_ACCOUNT_2   = 'orbaallegro/template_payment/bank_account_2';
    
    // Shipping
    const XML_PATH_TEMPLATE_SHIPPING_FREE_SHIPPING   = 'orbaallegro/template_shipping/free_shipping';
    const XML_PATH_TEMPLATE_SHIPPING_SHIPPING_PAY_SELLER_PRICE  = 'orbaallegro/template_shipping/shipping_pay_seller_price';
    const XML_PATH_TEMPLATE_SHIPPING_SHIPPING_PAYER  = 'orbaallegro/template_shipping/shipping_payer';
    const XML_PATH_TEMPLATE_SHIPPING_SHIPPING_INFO   = 'orbaallegro/template_shipping/shipping_info';
    const XML_PATH_TEMPLATE_SHIPPING_SHIPPING_OPTIONS= 'orbaallegro/template_shipping/shipping_options';
    const XML_PATH_TEMPLATE_SHIPPING_SHIPPING_TIME= 'orbaallegro/template_shipping/shipping_time';
    
    // Factory code :)
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC = "orbaallegro/template_shipping/first_item_postal_parcel_economic";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_PRIORITY = "orbaallegro/template_shipping/first_item_postal_parcel_priority";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_ECONOMIC = "orbaallegro/template_shipping/first_item_postal_letter_economic";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_PRIORITY = "orbaallegro/template_shipping/first_item_postal_letter_priority";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED = "orbaallegro/template_shipping/first_item_postal_letter_economic_registered";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED = "orbaallegro/template_shipping/first_item_postal_letter_priority_registered";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD = "orbaallegro/template_shipping/first_item_postal_parcel_economic_cod";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD = "orbaallegro/template_shipping/first_item_postal_parcel_priority_cod";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_COURIER_PARCEL = "orbaallegro/template_shipping/first_item_courier_parcel";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_COURIER_PARCEL_COD = "orbaallegro/template_shipping/first_item_courier_parcel_cod";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_BUSINESS_PARCEL = "orbaallegro/template_shipping/first_item_business_parcel";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_BUSINESS_PARCEL_COD = "orbaallegro/template_shipping/first_item_business_parcel_cod";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_EPRZESYLKA = "orbaallegro/template_shipping/first_item_pickpoint_eprzesylka";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_RUCH = "orbaallegro/template_shipping/first_item_pickpoint_ruch";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_PACZKOMATY = "orbaallegro/template_shipping/first_item_pickpoint_paczkomaty";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_DHL = "orbaallegro/template_shipping/first_item_pickpoint_cod_dhl";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA = "orbaallegro/template_shipping/first_item_pickpoint_cod_eprzesylka";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_RUCH = "orbaallegro/template_shipping/first_item_pickpoint_cod_ruch";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY = "orbaallegro/template_shipping/first_item_pickpoint_cod_paczkomaty";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC = "orbaallegro/template_shipping/next_item_postal_parcel_economic";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_PRIORITY = "orbaallegro/template_shipping/next_item_postal_parcel_priority";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_ECONOMIC = "orbaallegro/template_shipping/next_item_postal_letter_economic";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_PRIORITY = "orbaallegro/template_shipping/next_item_postal_letter_priority";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED = "orbaallegro/template_shipping/next_item_postal_letter_economic_registered";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED = "orbaallegro/template_shipping/next_item_postal_letter_priority_registered";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD = "orbaallegro/template_shipping/next_item_postal_parcel_economic_cod";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD = "orbaallegro/template_shipping/next_item_postal_parcel_priority_cod";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_COURIER_PARCEL = "orbaallegro/template_shipping/next_item_courier_parcel";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_COURIER_PARCEL_COD = "orbaallegro/template_shipping/next_item_courier_parcel_cod";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_BUSINESS_PARCEL = "orbaallegro/template_shipping/next_item_business_parcel";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_BUSINESS_PARCEL_COD = "orbaallegro/template_shipping/next_item_business_parcel_cod";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_EPRZESYLKA = "orbaallegro/template_shipping/next_item_pickpoint_eprzesylka";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_RUCH = "orbaallegro/template_shipping/next_item_pickpoint_ruch";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_PACZKOMATY = "orbaallegro/template_shipping/next_item_pickpoint_paczkomaty";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_DHL = "orbaallegro/template_shipping/next_item_pickpoint_cod_dhl";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA = "orbaallegro/template_shipping/next_item_pickpoint_cod_eprzesylka";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_RUCH = "orbaallegro/template_shipping/next_item_pickpoint_cod_ruch";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY = "orbaallegro/template_shipping/next_item_pickpoint_cod_paczkomaty";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC = "orbaallegro/template_shipping/item_count_postal_parcel_economic";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_PRIORITY = "orbaallegro/template_shipping/item_count_postal_parcel_priority";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_ECONOMIC = "orbaallegro/template_shipping/item_count_postal_letter_economic";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_PRIORITY = "orbaallegro/template_shipping/item_count_postal_letter_priority";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED = "orbaallegro/template_shipping/item_count_postal_letter_economic_registered";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED = "orbaallegro/template_shipping/item_count_postal_letter_priority_registered";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD = "orbaallegro/template_shipping/item_count_postal_parcel_economic_cod";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD = "orbaallegro/template_shipping/item_count_postal_parcel_priority_cod";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_COURIER_PARCEL = "orbaallegro/template_shipping/item_count_courier_parcel";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_COURIER_PARCEL_COD = "orbaallegro/template_shipping/item_count_courier_parcel_cod";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_BUSINESS_PARCEL = "orbaallegro/template_shipping/item_count_business_parcel";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_BUSINESS_PARCEL_COD = "orbaallegro/template_shipping/item_count_business_parcel_cod";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_EPRZESYLKA = "orbaallegro/template_shipping/item_count_pickpoint_eprzesylka";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_RUCH = "orbaallegro/template_shipping/item_count_pickpoint_ruch";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_PACZKOMATY = "orbaallegro/template_shipping/item_count_pickpoint_paczkomaty";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_DHL = "orbaallegro/template_shipping/item_count_pickpoint_cod_dhl";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA = "orbaallegro/template_shipping/item_count_pickpoint_cod_eprzesylka";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_RUCH = "orbaallegro/template_shipping/item_count_pickpoint_cod_ruch";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY = "orbaallegro/template_shipping/item_count_pickpoint_cod_paczkomaty";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PACZKA24 = "orbaallegro/template_shipping/first_item_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PACZKA48 = "orbaallegro/template_shipping/first_item_paczka48";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 = "orbaallegro/template_shipping/first_item_pickpoint_after_prepaid_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_PACZKA24 = "orbaallegro/template_shipping/first_item_pickpoint_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PACZKA24 = "orbaallegro/template_shipping/item_count_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PACZKA48 = "orbaallegro/template_shipping/item_count_paczka48";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24 = "orbaallegro/template_shipping/item_count_pickpoint_after_prepaid_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_PACZKA24 = "orbaallegro/template_shipping/item_count_pickpoint_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PACZKA24 = "orbaallegro/template_shipping/next_item_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PACZKA48 = "orbaallegro/template_shipping/next_item_paczka48";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 = "orbaallegro/template_shipping/next_item_pickpoint_after_prepaid_paczka24";
    const XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_PACZKA24 = "orbaallegro/template_shipping/next_item_pickpoint_paczka24";
    
    
    /**
        self::FIELD_NEXT_ITEM_PACZKA24,
        self::FIELD_NEXT_ITEM_PACZKA48,
        self::FIELD_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        self::FIELD_NEXT_ITEM_PICKPOINT_PACZKA24,
        self::FIELD_FIRST_ITEM_PACZKA24,
        self::FIELD_FIRST_ITEM_PACZKA48,
        self::FIELD_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        self::FIELD_FIRST_ITEM_PICKPOINT_PACZKA24,
     */
    
    // Location
    
    public function getCountry($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_LOCALIZATION_COUNTRY, $store, $website);
    }
    
    public function getProvince($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_LOCALIZATION_PROVINCE, $store, $website);
    }
    
    public function getCity($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_LOCALIZATION_CITY, $store, $website);
    }
    
    public function getPostcode($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_LOCALIZATION_POST_CODE, $store, $website);
    }
    
    // Images
    
    public function getImageCount($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_IMAGE_COUNT, $store, $website);
    }
    
    // Base 
    
    public function getDuration($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_DURATION, $store, $website);
    }
    
    public function getQuantity($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_QUANTITY, $store, $website);
    }
    
    public function getQuantityType($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_QUANTITY_TYPE, $store, $website);
    }
    
    public function getAdditionalOptions($store=null, $website=null) {
        return $this->_toArray($this->getConfig(self::XML_PATH_TEMPLATE_BASE_ADDITIONAL_OPTIONS, $store, $website));
    }
    
    public function getSalesFormat($store=null, $website=null) {
        return $this->_toArray($this->getConfig(self::XML_PATH_TEMPLATE_BASE_SALES_FORMAT, $store, $website));
    }
    
    public function getAutoRenew($store=null, $website=null) {
        return $this->_toArray($this->getConfig(self::XML_PATH_TEMPLATE_BASE_AUTO_RENEW, $store, $website));
    }
    
    // Shipping
    
    public function getShippingPayer($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_SHIPPING_PAYER, $store, $website);
    }
    
    public function getShippingPaySellerPrice($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_SHIPPING_PAY_SELLER_PRICE, $store, $website);
    }
    
    public function getShippingInfo($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_SHIPPING_INFO, $store, $website);
    }
    
    public function getFreeShipping($store=null, $website=null) {
        return $this->_toArray($this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FREE_SHIPPING, $store, $website));        
    }
    
    public function getShippingOptions($store=null, $website=null) {
        return $this->_toArray($this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_SHIPPING_OPTIONS, $store, $website));        
    }
    
    public function getShippingTime($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_SHIPPING_TIME, $store, $website);
    }

    public function getFirstItemPostalParcelEconomic($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC, $store, $website);
    }

    public function getFirstItemPostalParcelPriority($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_PRIORITY, $store, $website);
    }

    public function getFirstItemPostalLetterEconomic($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_ECONOMIC, $store, $website);
    }

    public function getFirstItemPostalLetterPriority($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_PRIORITY, $store, $website);
    }

    public function getFirstItemPostalLetterEconomicRegistered($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED, $store, $website);
    }

    public function getFirstItemPostalLetterPriorityRegistered($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED, $store, $website);
    }

    public function getFirstItemPostalParcelEconomicCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD, $store, $website);
    }

    public function getFirstItemPostalParcelPriorityCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD, $store, $website);
    }

    public function getFirstItemCourierParcel($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_COURIER_PARCEL, $store, $website);
    }

    public function getFirstItemCourierParcelCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_COURIER_PARCEL_COD, $store, $website);
    }
    
    public function getFirstItemBusinessParcel($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_BUSINESS_PARCEL, $store, $website);
    }

    public function getFirstItemBusinessParcelCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_BUSINESS_PARCEL_COD, $store, $website);
    }
    public function getFirstItemPickpointEprzesylka($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_EPRZESYLKA, $store, $website);
    }

    public function getFirstItemPickpointRuch($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_RUCH, $store, $website);
    }

    public function getFirstItemPickpointPaczkomaty($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_PACZKOMATY, $store, $website);
    }

    public function getFirstItemPickpointCodDhl($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_DHL, $store, $website);
    }

    public function getFirstItemPickpointCodEprzesylka($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA, $store, $website);
    }

    public function getFirstItemPickpointCodRuch($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_RUCH, $store, $website);
    }

    public function getFirstItemPickpointCodPaczkomaty($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY, $store, $website);
    }

    public function getNextItemPostalParcelEconomic($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC, $store, $website);
    }

    public function getNextItemPostalParcelPriority($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_PRIORITY, $store, $website);
    }

    public function getNextItemPostalLetterEconomic($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_ECONOMIC, $store, $website);
    }

    public function getNextItemPostalLetterPriority($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_PRIORITY, $store, $website);
    }

    public function getNextItemPostalLetterEconomicRegistered($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED, $store, $website);
    }

    public function getNextItemPostalLetterPriorityRegistered($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED, $store, $website);
    }

    public function getNextItemPostalParcelEconomicCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD, $store, $website);
    }

    public function getNextItemPostalParcelPriorityCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD, $store, $website);
    }

    public function getNextItemCourierParcel($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_COURIER_PARCEL, $store, $website);
    }

    public function getNextItemCourierParcelCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_COURIER_PARCEL_COD, $store, $website);
    }

    public function getNextItemBusinessParcel($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_BUSINESS_PARCEL, $store, $website);
    }

    public function getNextItemBusinessParcelCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_BUSINESS_PARCEL_COD, $store, $website);
    }
    
    public function getNextItemPickpointEprzesylka($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_EPRZESYLKA, $store, $website);
    }

    public function getNextItemPickpointRuch($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_RUCH, $store, $website);
    }

    public function getNextItemPickpointPaczkomaty($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_PACZKOMATY, $store, $website);
    }

    public function getNextItemPickpointCodDhl($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_DHL, $store, $website);
    }

    public function getNextItemPickpointCodEprzesylka($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA, $store, $website);
    }

    public function getNextItemPickpointCodRuch($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_RUCH, $store, $website);
    }

    public function getNextItemPickpointCodPaczkomaty($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY, $store, $website);
    }

    public function getItemCountPostalParcelEconomic($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC, $store, $website);
    }

    public function getItemCountPostalParcelPriority($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_PRIORITY, $store, $website);
    }

    public function getItemCountPostalLetterEconomic($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_ECONOMIC, $store, $website);
    }

    public function getItemCountPostalLetterPriority($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_PRIORITY, $store, $website);
    }

    public function getItemCountPostalLetterEconomicRegistered($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED, $store, $website);
    }

    public function getItemCountPostalLetterPriorityRegistered($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED, $store, $website);
    }

    public function getItemCountPostalParcelEconomicCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD, $store, $website);
    }

    public function getItemCountPostalParcelPriorityCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD, $store, $website);
    }

    public function getItemCountCourierParcel($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_COURIER_PARCEL, $store, $website);
    }

    public function getItemCountCourierParcelCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_COURIER_PARCEL_COD, $store, $website);
    }

    public function getItemCountBusinessParcel($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_BUSINESS_PARCEL, $store, $website);
    }

    public function getItemCountBusinessParcelCod($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_BUSINESS_PARCEL_COD, $store, $website);
    }
    
    public function getItemCountPickpointEprzesylka($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_EPRZESYLKA, $store, $website);
    }

    public function getItemCountPickpointRuch($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_RUCH, $store, $website);
    }

    public function getItemCountPickpointPaczkomaty($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_PACZKOMATY, $store, $website);
    }

    public function getItemCountPickpointCodDhl($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_DHL, $store, $website);
    }

    public function getItemCountPickpointCodEprzesylka($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA, $store, $website);
    }

    public function getItemCountPickpointCodRuch($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_RUCH, $store, $website);
    }

    public function getItemCountPickpointCodPaczkomaty($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY, $store, $website);
    }
    
    // New
    public function getFirstItemPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PACZKA24, $store, $website);
    }
    
    public function getFirstItemPaczka48($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PACZKA48, $store, $website);
    }
    
    public function getFirstItemPickpointAfterPrepaidPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24, $store, $website);
    }
    public function getFirstItemPickpointPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_FIRST_ITEM_PICKPOINT_PACZKA24, $store, $website);
    }
    
    public function getNextItemPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PACZKA24, $store, $website);
    }
    
    public function getNextItemPaczka48($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PACZKA48, $store, $website);
    }
    
    public function getNextItemPickpointAfterPrepaidPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24, $store, $website);
    }
    public function getNextItemPickpointPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_NEXT_ITEM_PICKPOINT_PACZKA24, $store, $website);
    }

    public function getItemCountPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PACZKA24, $store, $website);
    }
    
    public function getItemCountPaczka48($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PACZKA48, $store, $website);
    }
    
    public function getItemCountPickpointAfterPrepaidPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24, $store, $website);
    }
    public function getItemCountPickpointPaczka24($store = null, $website = null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_SHIPPING_ITEM_COUNT_PICKPOINT_PACZKA24, $store, $website);
    }


    // Payment
    
    public function getPaymentTypes($store=null, $website=null) {
        return $this->_toArray($this->getConfig(self::XML_PATH_TEMPLATE_PAYMENT_PAYMENT_TYPES, $store, $website));
    }
    
    public function getBankAccount1($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_PAYMENT_BANK_ACCOUNT_1, $store, $website);
    }
    
    public function getBankAccount2($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_PAYMENT_BANK_ACCOUNT_2, $store, $website);
    }
    

    
    // Attribute mapping
    
    public function getTitleAttribute($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_TITLE, $store, $website);
    }
    
    
    public function getDescriptionAttribute($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_DESCRIPTION, $store, $website);
    }
    
    public function getWeightAttribute($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_WEIGHT, $store, $website);
    }
    
    public function getBuyNowPriceAttribute($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_BASE_BUY_NOW_PRICE, $store, $website);
    }
    
    public function getMainImageAttribute($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_TEMPLATE_IMAGE_MAIN, $store, $website);
    }
    
    // Magic method
    public function getValueByField($fieldName, $store=null, $website=null) {
        $method = 'get'.$this->_camelize($fieldName);
        if(method_exists($this, $method)){
            return $this->$method($store, $website);
        }
        return null;
    }
    

}