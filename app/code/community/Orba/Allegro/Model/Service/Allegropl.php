<?php
class Orba_Allegro_Model_Service_Allegropl extends Orba_Allegro_Model_Service_Abstract
{
    protected $_code = Orba_Allegro_Model_Service::CODE_ALLEGROPL;
    protected $_countryCode = Orba_Allegro_Model_Service::ID_ALLEGROPL;
    protected $_surchargeSumText = "Suma";
    
    // Form ids for allegro.pl
    const ID_COUNTRY = 9;
    const ID_PROVINCE = 10;
    const ID_CITY = 11;
    const ID_POSTCODE = 32;
    
    const ID_IMAGE_1 = 16;
    const ID_IMAGE_2 = 17;
    const ID_IMAGE_3 = 18;
    const ID_IMAGE_4 = 19;
    const ID_IMAGE_5 = 20;
    const ID_IMAGE_6 = 21;
    const ID_IMAGE_7 = 22;
    const ID_IMAGE_8 = 23;
    
    const ID_TITLE = 1;
    const ID_CATEGORY = 2;
    const ID_STARTING_TIME = 3;
    const ID_DURATION = 4;
    const ID_QUANTITY = 5; /* @todo Process by stock item? */
    const ID_QUANTITY_TYPE = 28;
    const ID_ADDITIONAL_OPTIONS = 15;
    const ID_SALES_FORMAT = 29;
    const ID_AUTO_RENEW = 30;
    const ID_DESCRIPTION = 24;
    const ID_EXTENDED_DESCRIPTION = 25;
    const ID_BUY_NOW_PRICE = 8;
    
    const ID_PAYMENT_TYPES = 14;
    const ID_PAYMENT_BANK_ACCOUNT_1 = 33;
    const ID_PAYMENT_BANK_ACCOUNT_2 = 34;
       
    const ID_SHIPPING_INFO = 27;
    const ID_SHIPPING_PAYER = 12;
    const ID_SHIPPING_OPTIONS = 13;
    const ID_FREE_SHIPPING = 35;
    
    // Factored :)
    const ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC = 36;
    const ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY = 38;
    const ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC = 37;
    const ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY = 39;
    const ID_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED = 41;
    const ID_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED = 43;
    const ID_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD = 40;
    const ID_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD = 42;
    const ID_FIRST_ITEM_COURIER_PARCEL = 44;
    const ID_FIRST_ITEM_COURIER_PARCEL_COD = 45;
    
    const ID_FIRST_ITEM_BUSINESS_PARCEL = 53;
    const ID_FIRST_ITEM_BUSINESS_PARCEL_COD = 54;
    const ID_FIRST_ITEM_PACZKA24 = 55;
    const ID_FIRST_ITEM_PACZKA48 = 56;
    const ID_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 = 57;
    const ID_FIRST_ITEM_PICKPOINT_PACZKA24 = 58;

    const ID_FIRST_ITEM_PICKPOINT_EPRZESYLKA = 52;
    const ID_FIRST_ITEM_PICKPOINT_RUCH = 48;
    const ID_FIRST_ITEM_PICKPOINT_PACZKOMATY = 49;
    const ID_FIRST_ITEM_PICKPOINT_COD_DHL = 50;
    const ID_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA = 51;
    const ID_FIRST_ITEM_PICKPOINT_COD_RUCH = 46;
    const ID_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY = 47;
    
    const ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC = 136;
    const ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY = 138;
    const ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC = 137;
    const ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY = 139;
    const ID_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED = 141;
    const ID_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED = 143;
    const ID_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD = 140;
    const ID_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD = 142;
    const ID_NEXT_ITEM_COURIER_PARCEL = 144;
    const ID_NEXT_ITEM_COURIER_PARCEL_COD = 145;
    
    const ID_NEXT_ITEM_PICKPOINT_EPRZESYLKA = 152;
    const ID_NEXT_ITEM_PICKPOINT_RUCH = 148;
    const ID_NEXT_ITEM_PICKPOINT_PACZKOMATY = 149;
    const ID_NEXT_ITEM_PICKPOINT_COD_DHL = 150;
    const ID_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA = 151;
    const ID_NEXT_ITEM_PICKPOINT_COD_RUCH = 146;
    const ID_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY = 147;
    const ID_NEXT_ITEM_BUSINESS_PARCEL = 153;
    const ID_NEXT_ITEM_BUSINESS_PARCEL_COD = 154;    
    const ID_NEXT_ITEM_PACZKA24 = 155;
    const ID_NEXT_ITEM_PACZKA48 = 156;
    const ID_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 = 157;
    const ID_NEXT_ITEM_PICKPOINT_PACZKA24 = 158;
    
    const ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC = 236;
    const ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY = 238;
    const ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC = 237;
    const ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY = 239;
    const ID_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED = 241;
    const ID_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED = 243;
    const ID_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD = 240;
    const ID_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD = 242;
    const ID_ITEM_COUNT_COURIER_PARCEL = 244;
    const ID_ITEM_COUNT_COURIER_PARCEL_COD = 245;
    
    const ID_ITEM_COUNT_PICKPOINT_EPRZESYLKA = 252;//?
    const ID_ITEM_COUNT_PICKPOINT_RUCH = 248;
    const ID_ITEM_COUNT_PICKPOINT_PACZKOMATY = 249;
    const ID_ITEM_COUNT_PICKPOINT_COD_DHL = 250;
    const ID_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA = 251;
    const ID_ITEM_COUNT_PICKPOINT_COD_RUCH = 246;
    const ID_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY = 247;
    const ID_ITEM_COUNT_BUSINESS_PARCEL = 253;
    const ID_ITEM_COUNT_BUSINESS_PARCEL_COD = 254;    
    const ID_ITEM_COUNT_PACZKA24 = 255;
    const ID_ITEM_COUNT_PACZKA48 = 256;
    const ID_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24 = 257;
    const ID_ITEM_COUNT_PICKPOINT_PACZKA24 = 258;

    const ID_EAN = 337; // europejski kod towarowy
    const ID_SHIPPING_TIME = 340;
    const ID_SHOP_CATEGORY = 31;
    
    // pickpoints providers
    const ID_PICKPOINT_PROVIDER_DHL = 1;
    const ID_PICKPOINT_PROVIDER_RUCH = 2;
    const ID_PICKPOINT_PROVIDER_PACZKOMATY = 3;
    const ID_PICKPOINT_PROVIDER_EPRZESYLKA = 4;
    
    // pickpoints providers names
    const NAME_PICKPOINT_PROVIDER_DHL = 'DHL';
    const NAME_PICKPOINT_PROVIDER_RUCH = 'RUCH';
    const NAME_PICKPOINT_PROVIDER_PACZKOMATY = 'PACZKOMATY';
    const NAME_PICKPOINT_PROVIDER_EPRZESYLKA = 'E-PRZESYŁKA';
   
    // pickpoints
    const ID_PICKPOINT_COD_DHL = 10005;
    const ID_PICKPOINT_COD_RUCH = 10006;
    const ID_PICKPOINT_COD_PACZKOMATY = 10022;
    const ID_PICKPOINT_COD_EPRZESYLKA = 10061;
    const ID_PICKPOINT_RUCH = 20006;
    const ID_PICKPOINT_PACZKOMATY = 20022;
    const ID_PICKPOINT_EPRZESYLKA = 20061;

    protected $_pickpointProviderIds = array(
        self::ID_PICKPOINT_PROVIDER_DHL,
        self::ID_PICKPOINT_PROVIDER_RUCH,
        self::ID_PICKPOINT_PROVIDER_PACZKOMATY,
        self::ID_PICKPOINT_PROVIDER_EPRZESYLKA
    );
    
    protected $_pickpointIds = array(
        self::ID_PICKPOINT_COD_DHL,
        self::ID_PICKPOINT_COD_RUCH,
        self::ID_PICKPOINT_COD_PACZKOMATY,
        self::ID_PICKPOINT_COD_EPRZESYLKA,
        self::ID_PICKPOINT_RUCH,
        self::ID_PICKPOINT_PACZKOMATY,
        self::ID_PICKPOINT_EPRZESYLKA
    );
    
    protected $_providerByPickpoint = array(
        self::ID_PICKPOINT_COD_DHL => self::ID_PICKPOINT_PROVIDER_DHL,
        self::ID_PICKPOINT_COD_RUCH => self::ID_PICKPOINT_PROVIDER_RUCH,
        self::ID_PICKPOINT_COD_PACZKOMATY  => self::ID_PICKPOINT_PROVIDER_PACZKOMATY,
        self::ID_PICKPOINT_COD_EPRZESYLKA  => self::ID_PICKPOINT_PROVIDER_EPRZESYLKA,
        self::ID_PICKPOINT_RUCH  => self::ID_PICKPOINT_PROVIDER_RUCH,
        self::ID_PICKPOINT_PACZKOMATY => self::ID_PICKPOINT_PROVIDER_PACZKOMATY,
        self::ID_PICKPOINT_EPRZESYLKA => self::ID_PICKPOINT_PROVIDER_EPRZESYLKA
    );
    
    protected $_providerNameById = array(
        self::ID_PICKPOINT_PROVIDER_DHL => self::NAME_PICKPOINT_PROVIDER_DHL,
        self::ID_PICKPOINT_PROVIDER_RUCH => self::NAME_PICKPOINT_PROVIDER_RUCH,
        self::ID_PICKPOINT_PROVIDER_PACZKOMATY => self::NAME_PICKPOINT_PROVIDER_PACZKOMATY,
        self::ID_PICKPOINT_PROVIDER_EPRZESYLKA => self::NAME_PICKPOINT_PROVIDER_EPRZESYLKA
    );
    
    /**
     * @todo: add another fields
     * @var array
     */
    protected $_ignoredInEdit = array(
        // self::ID_DESCRIPTION, <- disable only if auction has offer(s)
        self::ID_DURATION,
        self::ID_STARTING_TIME
    );
            
    public function isShopAvailable() {
        return true;
    }

    public function getAuctionLink($auctionId) {
        return "http://allegro.pl/show_item.php?item=" . $auctionId;
    }
    
    public function getAboutLink($userId) {
        return "http://allegro.pl/my_page.php?uid=" . $userId;
    }

    public function getAucitonListLink($userId) {
        return "http://allegro.pl/show_user_auctions.php?uid=" . $userId;
    }

    public function getCommentsLink($userId) {
        return "http://allegro.pl/show_user.php?uid=" . $userId;
    }
    
    public function getShopLink($userId) {
        if($this->isShopAvailable()){
            return "http://allegro.pl/sklep/" . $userId . "_";
        }
        return null;
    }

    public function getAddFavouritesLink($userId) {
        return "http://allegro.pl/myaccount/favourites/favourites_sellers.php/addNew/?userId=" . $userId;
    }
    
    public function getContactLink($userId) {
        return "http://allegro.pl/SendMailToUser.php?userId=" . $userId;
    }
}