<?php
class Orba_Allegro_Model_Form_Auction extends Orba_Allegro_Model_Form_Abstract{

    /**
     * Code for quantity field
     */
    const QTY_FIELD_CODE = "quantity";
    /**
     * Detect to get product qty from Magento
     */
    const DEFAULT_QTY_VALUE = "0";

    const FIELD_CATEGORY = "category";
    const FIELD_COUNTRY = "country";
    const FIELD_PROVINCE = "province";
    const FIELD_CITY = "city";
    const FIELD_POSTCODE = "postcode";
    
    const FIELD_IMAGE_1 = "image_1";
    const FIELD_IMAGE_2 = "image_2";
    const FIELD_IMAGE_3 = "image_3";
    const FIELD_IMAGE_4 = "image_4";
    const FIELD_IMAGE_5 = "image_5";
    const FIELD_IMAGE_6 = "image_6";
    const FIELD_IMAGE_7 = "image_7";
    const FIELD_IMAGE_8 = "image_8";

    const FIELD_TITLE = "title";
    const FIELD_DURATION = "duration";
    const FIELD_EXTENDED_DESCRIPTION = "extended_description";
    const FIELD_DESCRIPTION = "description";
    const FIELD_STARTING_TIME = "starting_time";
    const FIELD_QUANTITY = "quantity";
    const FIELD_QUANTITY_TYPE = "quantity_type";
    const FIELD_BUY_NOW_PRICE = "buy_now_price";
    const FIELD_ADDITIONAL_OPTIONS = "additional_options";
    const FIELD_SALES_FORMAT = "sales_format";
    const FIELD_AUTO_RENEW = "auto_renew";
    
    const FIELD_PAYMENT_TYPES = "payment_types";
    const FIELD_BANK_ACCOUNT_1 = 'bank_account_1';
    const FIELD_BANK_ACCOUNT_2 = 'bank_account_2';
    
    const FIELD_FREE_SHIPPING  = 'free_shipping';
    const FIELD_SHIPPING_PAYER  = 'shipping_payer';
    const FIELD_SHIPPING_PAY_SELLER_PRICE = 'shipping_pay_seller_price';
    const FIELD_SHIPPING_INFO  = 'shipping_info';
    const FIELD_SHIPPING_OPTIONS  = 'shipping_options';
    const FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC = 'first_item_postal_parcel_economic';
    const FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY = 'first_item_postal_parcel_priority';
    const FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC = 'first_item_postal_letter_economic';
    const FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY = 'first_item_postal_letter_priority';
    const FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED = 'first_item_postal_letter_economic_registered';
    const FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED = 'first_item_postal_letter_priority_registered';
    const FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD = 'first_item_postal_parcel_economic_cod';
    const FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD = 'first_item_postal_parcel_priority_cod';
    const FIELD_FIRST_ITEM_COURIER_PARCEL = 'first_item_courier_parcel';
    const FIELD_FIRST_ITEM_COURIER_PARCEL_COD = 'first_item_courier_parcel_cod';
    const FIELD_FIRST_ITEM_BUSINESS_PARCEL = 'first_item_business_parcel';
    const FIELD_FIRST_ITEM_BUSINESS_PARCEL_COD = 'first_item_business_parcel_cod';
    const FIELD_FIRST_ITEM_PICKPOINT_EPRZESYLKA = 'first_item_pickpoint_eprzesylka';
    const FIELD_FIRST_ITEM_PICKPOINT_RUCH = 'first_item_pickpoint_ruch';
    const FIELD_FIRST_ITEM_PICKPOINT_PACZKOMATY = 'first_item_pickpoint_paczkomaty';
    const FIELD_FIRST_ITEM_PICKPOINT_COD_DHL = 'first_item_pickpoint_cod_dhl';
    const FIELD_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA = 'first_item_pickpoint_cod_eprzesylka';
    const FIELD_FIRST_ITEM_PICKPOINT_COD_RUCH = 'first_item_pickpoint_cod_ruch';
    const FIELD_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY = 'first_item_pickpoint_cod_paczkomaty';
    const FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC = 'next_item_postal_parcel_economic';
    const FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY = 'next_item_postal_parcel_priority';
    const FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC = 'next_item_postal_letter_economic';
    const FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY = 'next_item_postal_letter_priority';
    const FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED = 'next_item_postal_letter_economic_registered';
    const FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED = 'next_item_postal_letter_priority_registered';
    const FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD = 'next_item_postal_parcel_economic_cod';
    const FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD = 'next_item_postal_parcel_priority_cod';
    const FIELD_NEXT_ITEM_COURIER_PARCEL = 'next_item_courier_parcel';
    const FIELD_NEXT_ITEM_COURIER_PARCEL_COD = 'next_item_courier_parcel_cod';
    const FIELD_NEXT_ITEM_BUSINESS_PARCEL = 'next_item_business_parcel';
    const FIELD_NEXT_ITEM_BUSINESS_PARCEL_COD = 'next_item_business_parcel_cod';
    const FIELD_NEXT_ITEM_PICKPOINT_EPRZESYLKA = 'next_item_pickpoint_eprzesylka';
    const FIELD_NEXT_ITEM_PICKPOINT_RUCH = 'next_item_pickpoint_ruch';
    const FIELD_NEXT_ITEM_PICKPOINT_PACZKOMATY = 'next_item_pickpoint_paczkomaty';
    const FIELD_NEXT_ITEM_PICKPOINT_COD_DHL = 'next_item_pickpoint_cod_dhl';
    const FIELD_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA = 'next_item_pickpoint_cod_eprzesylka';
    const FIELD_NEXT_ITEM_PICKPOINT_COD_RUCH = 'next_item_pickpoint_cod_ruch';
    const FIELD_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY = 'next_item_pickpoint_cod_paczkomaty';
    const FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC = 'item_count_postal_parcel_economic';
    const FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY = 'item_count_postal_parcel_priority';
    const FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC = 'item_count_postal_letter_economic';
    const FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY = 'item_count_postal_letter_priority';
    const FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED = 'item_count_postal_letter_economic_registered';
    const FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED = 'item_count_postal_letter_priority_registered';
    const FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD = 'item_count_postal_parcel_economic_cod';
    const FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD = 'item_count_postal_parcel_priority_cod';
    const FIELD_ITEM_COUNT_COURIER_PARCEL = 'item_count_courier_parcel';
    const FIELD_ITEM_COUNT_COURIER_PARCEL_COD = 'item_count_courier_parcel_cod';
    const FIELD_ITEM_COUNT_BUSINESS_PARCEL = 'item_count_business_parcel';
    const FIELD_ITEM_COUNT_BUSINESS_PARCEL_COD = 'item_count_business_parcel_cod';
    const FIELD_ITEM_COUNT_PICKPOINT_EPRZESYLKA = 'item_count_pickpoint_eprzesylka';
    const FIELD_ITEM_COUNT_PICKPOINT_RUCH = 'item_count_pickpoint_ruch';
    const FIELD_ITEM_COUNT_PICKPOINT_PACZKOMATY = 'item_count_pickpoint_paczkomaty';
    const FIELD_ITEM_COUNT_PICKPOINT_COD_DHL = 'item_count_pickpoint_cod_dhl';
    const FIELD_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA = 'item_count_pickpoint_cod_eprzesylka';
    const FIELD_ITEM_COUNT_PICKPOINT_COD_RUCH = 'item_count_pickpoint_cod_ruch';
    const FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY = 'item_count_pickpoint_cod_paczkomaty';
    const FIELD_ITEM_COUNT_PACZKA24 = 'item_count_paczka24';
    const FIELD_ITEM_COUNT_PACZKA48 = 'item_count_paczka48';
    const FIELD_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24 = 'item_count_pickpoint_after_prepaid_paczka24';
    const FIELD_ITEM_COUNT_PICKPOINT_PACZKA24 = 'item_count_pickpoint_paczka24';
    const FIELD_NEXT_ITEM_PACZKA24 = 'next_item_paczka24';
    const FIELD_NEXT_ITEM_PACZKA48 = 'next_item_paczka48';
    const FIELD_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 = 'next_item_pickpoint_after_prepaid_paczka24';
    const FIELD_NEXT_ITEM_PICKPOINT_PACZKA24 = 'next_item_pickpoint_paczka24';
    const FIELD_FIRST_ITEM_PACZKA24 = 'first_item_paczka24';
    const FIELD_FIRST_ITEM_PACZKA48 = 'first_item_paczka48';
    const FIELD_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24 = 'first_item_pickpoint_after_prepaid_paczka24';
    const FIELD_FIRST_ITEM_PICKPOINT_PACZKA24 = 'first_item_pickpoint_paczka24';
    
    
    const FIELD_EAN = 'ean';
    const FIELD_SHIPPING_TIME = 'shipping_time';
    const FIELD_SHOP_CATEGORY = 'shop_category';

    protected $_clientMethod = "getSellFormFieldsExt";
    
    protected $_deliveryCostFirstItemFields = array(
        self::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC,
        self::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY,
        self::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC,
        self::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY,
        self::FIELD_FIRST_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        self::FIELD_FIRST_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        self::FIELD_FIRST_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        self::FIELD_FIRST_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        self::FIELD_FIRST_ITEM_COURIER_PARCEL,
        self::FIELD_FIRST_ITEM_COURIER_PARCEL_COD,
        self::FIELD_FIRST_ITEM_BUSINESS_PARCEL,
        self::FIELD_FIRST_ITEM_BUSINESS_PARCEL_COD,
        self::FIELD_FIRST_ITEM_PICKPOINT_EPRZESYLKA,
        self::FIELD_FIRST_ITEM_PICKPOINT_RUCH,
        self::FIELD_FIRST_ITEM_PICKPOINT_PACZKOMATY,
        self::FIELD_FIRST_ITEM_PICKPOINT_COD_DHL,
        self::FIELD_FIRST_ITEM_PICKPOINT_COD_EPRZESYLKA,
        self::FIELD_FIRST_ITEM_PICKPOINT_COD_RUCH,
        self::FIELD_FIRST_ITEM_PICKPOINT_COD_PACZKOMATY,
        self::FIELD_FIRST_ITEM_PACZKA24,
        self::FIELD_FIRST_ITEM_PACZKA48,
        self::FIELD_FIRST_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        self::FIELD_FIRST_ITEM_PICKPOINT_PACZKA24,
    );
    
    protected $_deliveryCostExtendFields = array(
        self::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC,
        self::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY,
        self::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC,
        self::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY,
        self::FIELD_NEXT_ITEM_POSTAL_LETTER_ECONOMIC_REGISTERED,
        self::FIELD_NEXT_ITEM_POSTAL_LETTER_PRIORITY_REGISTERED,
        self::FIELD_NEXT_ITEM_POSTAL_PARCEL_ECONOMIC_COD,
        self::FIELD_NEXT_ITEM_POSTAL_PARCEL_PRIORITY_COD,
        self::FIELD_NEXT_ITEM_COURIER_PARCEL,
        self::FIELD_NEXT_ITEM_COURIER_PARCEL_COD,
        self::FIELD_NEXT_ITEM_BUSINESS_PARCEL,
        self::FIELD_NEXT_ITEM_BUSINESS_PARCEL_COD,
        self::FIELD_NEXT_ITEM_PICKPOINT_EPRZESYLKA,
        self::FIELD_NEXT_ITEM_PICKPOINT_RUCH,
        self::FIELD_NEXT_ITEM_PICKPOINT_PACZKOMATY,
        self::FIELD_NEXT_ITEM_PICKPOINT_COD_DHL,
        self::FIELD_NEXT_ITEM_PICKPOINT_COD_EPRZESYLKA,
        self::FIELD_NEXT_ITEM_PICKPOINT_COD_RUCH,
        self::FIELD_NEXT_ITEM_PICKPOINT_COD_PACZKOMATY,
        self::FIELD_NEXT_ITEM_PACZKA24,
        self::FIELD_NEXT_ITEM_PACZKA48,
        self::FIELD_NEXT_ITEM_PICKPOINT_AFTER_PREPAID_PACZKA24,
        self::FIELD_NEXT_ITEM_PICKPOINT_PACZKA24,
        self::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC,
        self::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY,
        self::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC,
        self::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY,
        self::FIELD_ITEM_COUNT_POSTAL_LETTER_ECONOMIC_REGISTERED,
        self::FIELD_ITEM_COUNT_POSTAL_LETTER_PRIORITY_REGISTERED,
        self::FIELD_ITEM_COUNT_POSTAL_PARCEL_ECONOMIC_COD,
        self::FIELD_ITEM_COUNT_POSTAL_PARCEL_PRIORITY_COD,
        self::FIELD_ITEM_COUNT_COURIER_PARCEL,
        self::FIELD_ITEM_COUNT_COURIER_PARCEL_COD,
        self::FIELD_ITEM_COUNT_BUSINESS_PARCEL,
        self::FIELD_ITEM_COUNT_BUSINESS_PARCEL_COD,
        self::FIELD_ITEM_COUNT_PICKPOINT_EPRZESYLKA,
        self::FIELD_ITEM_COUNT_PICKPOINT_RUCH,
        self::FIELD_ITEM_COUNT_PICKPOINT_PACZKOMATY,
        self::FIELD_ITEM_COUNT_PICKPOINT_COD_DHL,
        self::FIELD_ITEM_COUNT_PICKPOINT_COD_EPRZESYLKA,
        self::FIELD_ITEM_COUNT_PICKPOINT_COD_RUCH,
        self::FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY,  
        self::FIELD_ITEM_COUNT_PICKPOINT_COD_PACZKOMATY,
        self::FIELD_ITEM_COUNT_PACZKA24,
        self::FIELD_ITEM_COUNT_PACZKA48,
        self::FIELD_ITEM_COUNT_PICKPOINT_AFTER_PREPAID_PACZKA24,
        self::FIELD_ITEM_COUNT_PICKPOINT_PACZKA24,
    );
    


    public function __construct($attributes = array()) {
        parent::__construct($attributes);
    }
    
    public function load($categoryId=null) {
        
        if($categoryId!==null){
            $this->_clientMethod = "getSellFormFieldsForCategory";
            $this->_clientMethodArgs = array("categoryId"=>(int)$categoryId);
        }
        return parent::load();
    }
    

    /**
     * @param string $fieldCode
     * @return null|Varien_Data_Form_Element_Abstract
     * @throws Orba_Allegro_Exception
     */
    public function getField($fieldCode) {
        if(!($cc=$this->getCountryCode())){
            throw new Orba_Allegro_Exception("No country code specified");
        }
        $map = $this->getMapping();
        
        if(false!==($fieldId = array_search($fieldCode, $map))){
            return $this->_getElement($fieldId);    
        }
        return null;
    }
    
    public function getService() {
        $factory = Mage::getSingleton('orbaallegro/service');
        return $factory::factory($this->getCountryCode());
    }
    
    public function getMapping($countryCode=null) {
        if(is_null($countryCode)){
            $countryCode = $this->getCountryCode();
        }
        $mapping = $this->_getFullMapping();
        if(isset($mapping[$countryCode])){
            return $mapping[$countryCode];
        }
        return array();
    }
	
	public function getSortOrder($countryCode=null) {
        if(is_null($countryCode)){
            $countryCode = $this->getCountryCode();
        }
        $sortOrder = $this->_getFullSortOrder();
        if(isset($sortOrder[$countryCode])){
            return $sortOrder[$countryCode];
        }
        return array();
    }
    
    public function getRevMapping($countryCode=null) {
        return array_flip($this->getMapping($countryCode));
    }
    
    /**
     * @todo move it to db
     */
    public function _getFullMapping() {
        if(!$this->getData("full_mapping")){
            
            $sellformMapping = Mage::getModel('orbaallegro/mapping_sellform');
            /*
             * @var $sellformMapping Orba_Allegro_Model_Mapping_Sellform
             */
            $fullMapping = $sellformMapping->getMapping();
            $this->setData("full_mapping", $fullMapping);
        }
        return $this->getData("full_mapping");
    }
	
	public function _getFullSortOrder() {
        if(!$this->getData("full_sort_order")){
            
            $sellformMapping = Mage::getModel('orbaallegro/mapping_sellform');
            /*
             * @var $sellformMapping Orba_Allegro_Model_Mapping_Sellform
             */
            $fullSortOrder = $sellformMapping->getSortOrder();
            $this->setData("full_sort_order", $fullSortOrder);
        }
        return $this->getData("full_sort_order");
    }
    
    public function getPrefix() {
        return "auction";
    }
    
    protected function _extract($data) {
        if(isset($data->sellFormFields) && isset($data->sellFormFields->item)){
            return parent::_extract($data->sellFormFields->item);
        }
        if(isset($data->sellFormFieldsForCategory) && 
           isset($data->sellFormFieldsForCategory->sellFormFieldsList) &&
           isset($data->sellFormFieldsForCategory->sellFormFieldsList->item)){
           return parent::_extract($data->sellFormFieldsForCategory->sellFormFieldsList->item);
        }
    }
    
    protected function _addMetaFields($item){
                $this->_fieldset->addField(
                $this->getPrefix().$item->sellFormId."_res", 
                "hidden",
                array(
                    "type"  => "hidden",
                    "value" => $item->sellFormResType,
                    "name"  => "res[".$item->sellFormId."]"
                )
        );
        $this->_fieldset->addField(
                $this->getPrefix().$item->sellFormId."_opt", 
                "hidden",
                array(
                    "type"  => "hidden",
                    "value" => $item->sellFormOptions,
                    "name"  => "opt[".$item->sellFormId."]"
                )
        );
        return parent::_addMetaFields($item);
    }
    
    protected function _processField($item) {
        if(!$this->getEditMode()){
            switch ($item->sellFormId) {
                // Skip extended desciption (available only in edit mode)
                case Orba_Allegro_Model_Service_Allegropl::ID_EXTENDED_DESCRIPTION:
                case Orba_Allegro_Model_Service_Webapi::ID_EXTENDED_DESCRIPTION:
                    return null;
                break;
            }
        }
        return parent::_processField($item);
    }
    
    /**
     * Prepare values based on config values
     * 
     * @param Orba_Allegro_Model_Auction_Config $config
     * @param Mage_Catalog_Model_Product $product
     * @param type $store
     * @return \Orba_Allegro_Model_Form_Auction
     */
    public function prepareConfigValues(
            Orba_Allegro_Model_Auction_Config $config, 
            Mage_Catalog_Model_Product $product, 
            $store=null) {
        
        $mapping = $this->getMapping();
        foreach($mapping as $fieldCode){
            $field = $this->getField($fieldCode);
            if($field instanceof Varien_Data_Form_Element_Abstract){
                if ($fieldCode == self::QTY_FIELD_CODE) {
                    $defaultValue = $config->getValueByField($fieldCode, $store);
                    if ($defaultValue == self::DEFAULT_QTY_VALUE) {
                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                        $value = (int) $stock->getQty();
                    } else {
                        $value = $config->getValueByField($fieldCode, $store);
                    }
                } else {
                    $value = $config->getValueByField($fieldCode, $store);
                }
                if(!is_null($value) && ""!==$value){
                    $field->setValue($value);
                }
            }
        }
        
        ////////////////////////////////////////////////////////////////////////
        // Prepare attributes data
        ////////////////////////////////////////////////////////////////////////
        
        $titleField = $this->getField($this::FIELD_TITLE);
        $descriptionField = $this->getField($this::FIELD_DESCRIPTION);
        
        if(($value=$product->getData($config->getTitleAttribute($store))) && $titleField){
            if(!empty($value)){
                $titleField->setValue($value);
            }
        }
        
        if(($value=$product->getData($config->getDescriptionAttribute($store))) && $descriptionField){
            if(!empty($value)){
                $descriptionField->setValue($value);
            }
        }
        
        // Price
        if($value=$this->_getBruttoPrice($product, $config->getBuyNowPriceAttribute($store), $store)){
            if($field=$this->getField($this::FIELD_BUY_NOW_PRICE)){
                $helper = Mage::helper('core');
                /* @var $helper Mage_Core_Helper_Data */
                $price = $helper->currencyByStore($value, $store, false);
                $field->setValue($price);  
                // Who pays for shippings?
                if($shippingPayer = $this->getField($this::FIELD_SHIPPING_PAYER)){
                    $freeShippingPrice = $config->getValueByField($this::FIELD_SHIPPING_PAY_SELLER_PRICE, $store);
                    // Is free shipping?
                    if($freeShippingPrice && is_numeric($value) && (float)$freeShippingPrice>0 && $price > (float)$freeShippingPrice){
                        $shippingPayer->setValue(Orba_Allegro_Model_System_Config_Source_Shippingpayer::VALUE_SELLER);
                        // Need set all setted delivery price to 0
                        foreach($this->getAllDeliveryCostFirstItemFields() as $deliveryField){
                            if($deliveryField->getValue()!="-1"){
                                $deliveryField->setValue(0);
                            }
                        }
                        foreach($this->getAllDeliveryCostExtendedFields() as $deliveryField){
                            $deliveryField->setValue(-1);
                        }
                    }
                }
            }
        }
        
        ////////////////////////////////////////////////////////////////////////
        // Images from Simple Product or Parent Product
        ////////////////////////////////////////////////////////////////////////
        
        $imageCount = $config->getImageCount();
		$parent = $this->_getParentProduct();
        
        if(is_numeric($imageCount) && $imageCount>0){
            // Set image 1
            $usedImages = array();
            if($value=$product->getData($config->getMainImageAttribute($store))){
                if($field=$this->getField($this::FIELD_IMAGE_1)){
                    $usedImages[] = $value;
                    $value = $this->_getProcuctImgPath($value);
                    $field->setValue($value);
                }
            } elseif ($parent) {
				if ($value=$parent->getData($config->getMainImageAttribute($store))) {
					if ($field=$this->getField($this::FIELD_IMAGE_1)) {
						$usedImages[] = $value;
						$value = $this->_getProcuctImgPath($value);
						$field->setValue($value);
					}	
				}
			}
            if($imageCount>1){
                $i = 2;
                $mediaGallery = $product->getMediaGalleryImages();
				if (!$mediaGallery->getSize() && $parent) {
					$mediaGallery = $parent->getMediaGalleryImages();
				}
                foreach($mediaGallery as $image){
                    if(in_array($image, $usedImages)){
                        // Do not duplicate images
                        continue;
                    }
                    if($i<$imageCount){
                        $fieldCode = constant(get_class($this)."::"."FIELD_IMAGE_" . $i);
                        if(($fieldCode!==null) && ($field = $this->getField($fieldCode))){
                            $usedImages[] = $image->getFile();
                            $field->setValue($this->_getProcuctImgPath($image->getFile()));
                        }
                    }
                    $i++;
                }
            }
        }
      
        return $this;
    }
    
    /**
     * Return price always inc. tax
     * @todo invoke based on config?
     * @param Mage_Catalog_Model_Product $product
     * @param string $priceAttr
     * @param Mage_Core_Model_Store $store
     * @return null
     */
    protected function _getBruttoPrice(Mage_Catalog_Model_Product $product, $priceAttr, Mage_Core_Model_Store $store) {
        $priceValue = $product->getData($priceAttr);
        if($priceValue){
            $helper = Mage::helper('tax');
            /* @var $helper Mage_Tax_Helper_Data */
            
            if($helper->priceIncludesTax($store)){
                return $priceValue;
            }
            return $helper->getPrice($product, $priceValue, true, null, null, null, $store, false);
        }
        return null;
    }
    
    protected function get($param) {
        
    }
    
    /**
     * Preapre values based on template values
     * 
     * @param Orba_Allegro_Model_Template $template
     * @return \Orba_Allegro_Model_Form_Auction
     */
    public function prepareTemplateValues(Orba_Allegro_Model_Template $template){
        if(!$template || !$template->getId()){
            return $this;
        }
        
        $mappings = $this->getMapping();
        
        foreach($template->getAttributes() as $attribute){
            $code = $attribute->getAttributeCode();
            $allegorFormId = $attribute->getAllegroFormId();
            /* @var $attribute Mage_Eav_Model_Attribute */
            if(array_key_exists($allegorFormId, $mappings)){
                if($field = $this->getField($code)){
                    $value = null;
                    if($template->hasData($code)){
                        $value = $template->getData($code);
                        if(in_array($attribute->getBackendType(), array("text", "varchar"))){
                            if(empty($value)){
                                $value = null;
                            }
                        }
                    }
                    if($value!==null){
                        $field->setValue($value);
                    }
                }
            }
        }
        return $this;
    }
    
    /**
     * Prepare filtered values
     * 
     * @param Mage_Catalog_Model_Product $product
     */
    public function prepareFilteredValues(Mage_Catalog_Model_Product $product, Mage_Core_Model_Store $store, Orba_Allegro_Model_User $user){
        // @todo Move it to meta-attribute data
        $filteredFields = array(
            $this::FIELD_DESCRIPTION,
            $this::FIELD_TITLE
        );
        
        $filter = Mage::getModel("orbaallegro/template_filter");
        /* @var $filter Orba_Allegro_Model_Template_Filter */
        
        $filter->setStoreId($store->getId());
        
        foreach($filteredFields as $code){
            if($field=$this->getField($code)){
                $value = $field->getValue();
                if(!empty($value)){            
                    $filter->setProduct($product);
                    $filter->setUser($user);
                    $field->setValue($filter->filter($value));
                }
            }
        }
        
        return $this;
    }
    
    protected function _getProcuctImgPath($value) {
        if(empty($value) || $value=="no_selection"){
            return null;
        }
        return "catalog/product" . $value;
    }
    
    public function prepareAuctionValues($auction) {
        $data = $auction->getSerializedData();
        $data = $data['fields'];
        
        $parserIn = $this->getParser();
        /* @var $parserIn Orba_Allegro_Model_Form_Parser_In */
        $parserOut = Mage::getModel('orbaallegro/form_parser_out', $auction->getService());
        /* @var $parserOut Orba_Allegro_Model_Form_Parser_Out */
        
        foreach ($data as $item){
            if($field=$this->_getElement($item[$parserOut::KEY_FID])){
                if(($storedValue = $parserOut->extractValue($item, $field))!==null){
                    $value = $parserIn->parseAuctionValue(
                            $storedValue, 
                            $item[$parserOut::KEY_FID],  
                            $field->getAllegroType(), 
                            $auction
                    );
                    $field->setValue($value);
                }
            }
        }
		
		// In addition add renew type from auction
		if($field=$this->getField(self::FIELD_AUTO_RENEW)){
			$field->setValue($auction->getRenewType());
		}
    }
    
    public function getAllDeliveryCostFirstItemFields() {
        $fields = array();
        foreach($this->_deliveryCostFirstItemFields as $fieldCode){
            $field = $this->getField($fieldCode);
            if($field instanceof Varien_Data_Form_Element_Abstract){
                $fields[] = $field;
            }
        }
        return $fields;
    }
    
    public function getAllDeliveryCostExtendedFields() {
        $fields = array();
        foreach($this->_deliveryCostExtendFields as $fieldCode){
            $field = $this->getField($fieldCode);
            if($field instanceof Varien_Data_Form_Element_Abstract){
                $fields[] = $field;
            }
        }
        return $fields;
    }
    
	protected function _sortItems($items) {
		$sortOrder = $this->getSortOrder();
		$maxSortOrder = max($sortOrder);
		$i = 0;
		foreach ($items as $key => $item) {
			$i++;
			if (isset($sortOrder[$item->sellFormId])) {
				$items[$key]->sortOrder = $sortOrder[$item->sellFormId];
			} else {
				$items[$key]->sortOrder = $maxSortOrder + (int) $i;
			}
		}
		usort($items, function($a, $b) {
			return $a->sortOrder > $b->sortOrder ? 1 : ($a->sortOrder == $b->sortOrder ? 0 : -1);
		});
		return $items;
	}
	
    /**
	 * Get Parent Product Id
	 * 
     * @return int Parent Product Id
     */
    protected function _getParentProductId() {
        return Mage::registry('parent_product_id');
    }
    
	/**
	 * Get Parent Product
	 * 
	 * @return mixed boolean|Mage_Catalog_Model_Product 
	 */
	protected function _getParentProduct() {
		$parent = null;
		if ($this->_getParentProductId()) {
			$parent = Mage::getModel('catalog/product')->load($this->_getParentProductId());
			if (!$parent || !$parent->getId()) {
				$parent = false;
			}
		}
		
		return $parent;
	}
}
