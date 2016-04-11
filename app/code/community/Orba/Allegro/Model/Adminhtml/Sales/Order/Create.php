<?php

class Orba_Allegro_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create {
    
    public function extractNames($fullname) {
        if(is_null($fullname)){
            return array("", "");
        }
        $exploded = explode(" ", $fullname);
        $count = count($exploded);
        if($count==1){
            return array($fullname, "");
        }
        $lastname = array_splice($exploded, -1);
        return array(implode(" ", $exploded), $lastname[0]);
    }
    
    /**
     * @todo move fields names to fieldsets
     * @param Orba_Allegro_Model_Transaction $transaction
     * @return Mage_Sales_Model_Quote
     */
    public function createQuoteFromTransaction(Orba_Allegro_Model_Transaction $transaction, Mage_Core_Model_Store $store) {
        
        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        
        $shipment = $transaction->getShipment();
        $payment = $transaction->getPayment();
        
        $quote = $this->getQuote();
       
        $quote->addData(array(
            "store_id" => $store->getId(),
            "orbaallegro_transaction_id" => $transaction->getId(),
            "orbaallegro_shipment_id" => $shipment ? $shipment->getId() : null,
            "orbaallegro_payment_id" => $payment ? $payment->getId() : null,
            "orbaallegro_allegro_buyer_id" => $transaction->getAllegroBuyerId(),
            "orbaallegro_country_code" => $transaction->getCountryCode(),
            "orbaallegro_contractor_id" => $transaction->getContractorId(),
        ));
        
        /** Billing Address **/
        $billingAddress = $this->prepareAddress($transaction, 
                Orba_Allegro_Model_Transaction_Address::TYPE_BILLING);
        
        /** Shipping Address **/
        $shippingAddress = $this->prepareAddress($transaction, 
                Orba_Allegro_Model_Transaction_Address::TYPE_SHIPPING);
       
        
        /** Account **/
        $accountData = array(
            "email" => $transaction->getBuyerEmail(),
            "firstname" => $billingAddress['firstname'],
            "lastname" => $billingAddress['lastname']
        );
        
        /** Has account **/
        $customer = $this->findCustomer(
                $transaction->getBuyerEmail(), 
                $store->getWebsiteId()
        );
        
        /** Register User? **/
        if($customer && $customer->getId()){
            $quote->setCustomer($customer);
        }else{
            $quote->setCustomerIsGuest(!$this->getCreateCustomer($store));
        }
        
        /** Shipping **/
        $shippingMethodObject = $transaction->getCarrierMethod();
        
        /** Payment **/
        $paymentMethodObject = $transaction->getPaymentMethod();
        
        
        $this->importPostData(array(
            "account"           => $accountData,
            "billing_address"   => $billingAddress,
            "shipping_address"  => $shippingAddress,
            "shipping_method"   => $shippingMethodObject ? $shippingMethodObject->getCode() : null,
            "payment_method"    => $paymentMethodObject ? $paymentMethodObject->getCode() : null
        ));
        
        /** Items **/
        $this->addItemsFromTransaction($transaction, $store);        
        
        /** Collect shipping rates **/
        $quote->getShippingAddress()->requestShippingRates();
        
        /** Override shipping rates **/
        if($this->getOverrideRate($store)){
            $rates = $quote->getShippingAddress()->getShippingRatesCollection();
            foreach($rates as $rate){
                /* @var $rate Mage_Sales_Model_Quote_Address_Rate */
                $rate->setPrice($transaction->getTransactionCodAmount());
            }
        }
        return $quote;
    }
    
    public function prepareAddress(Orba_Allegro_Model_Transaction $transaction, $type) {
        
        $address = $transaction->getAddress($type);
        if(!$address){
            return array();
        }
        $namesShipping = $this->extractNames($address->getFullname());
        $out = array(
            "firstname"=> $namesShipping[0],
            "lastname"=>$namesShipping[1],
            "company"=>$address->getCompany(),
            "city"=>$address->getCity(),
            "street"=>$address->getStreet(),
            "telephone"=>$address->getPhone(),
            "postcode"=>$address->getPostcode(),
            "country_id"=>$address->getCountryId(),
            "vat_id"=>$address->getVatid(),
            "orbaallegro_shipment_id" => $address->getId(),
            "orbaallegro_address_id" => $address->getId(),
            "orbaallegro_address_buyer_id" => $transaction->getAllegroBuyerId()
        );
        /**
         * @todo add same_as_billing to transaction address, and fill during creation
         */
        if($type==Orba_Allegro_Model_Transaction_Address::TYPE_SHIPPING){
            $out['same_as_billing'] = (int)$address->getSameAsBilling();
        }
        return $out;
    }
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param flaot $priceValue
     * @param Mage_Core_Model_Store $store
     * @return float
     */
    protected function _getPriceForBackend($product, $priceValue, $store) {
        $helper = Mage::helper('tax');
            /* @var $helper Mage_Tax_Helper_Data */
        if($helper->priceIncludesTax($store)){
            return $priceValue;
        }
        return $helper->getPrice($product, $priceValue, false, null, null, null, $store, true);
    }
    
    /**
     * @param Orba_Allegro_Model_Transaction $transaction
     * @return Orba_Allegro_Model_Adminhtml_Sales_Order_Create
     */
    public function addItemsFromTransaction(Orba_Allegro_Model_Transaction $transaction) {
        
        
        foreach ($transaction->getTransactionAuctionCollection() as $auctionTransaction){
            $itemModel =  Mage::getModel("sales/quote_item");
            /* @var $itemModel Mage_Sales_Model_Quote_Item */
            
            // All prices includes tax
            $price = (float)$auctionTransaction->getPrice();
            
            $product = $auctionTransaction->getProduct();
            $auction = $auctionTransaction->getAuction();
            $store = $auction->getStore();
            
            $itemModel->setOrbaallegroAuctionId($auctionTransaction->getAuctionId());
            
            $itemModel->setStoreId($store->getId());
            $itemModel->setQuote($this->getQuote());
            $itemModel->setName($auctionTransaction->getTitle());
            $itemModel->setQty($auctionTransaction->getQuantity());		
            
            $setCustomPrice = true;
            if($product){
                $itemModel->setProduct($product);
                if((float)$product->getFinalPrice()==$price){
                    $setCustomPrice = false;
                }
            }
            
            if($setCustomPrice){
                $itemModel->setCustomPrice($price);
                $itemModel->setOriginalCustomPrice($price);
            }
            
            // Transfer bid if exists
            if($auctionTransaction->getBidId()){
                $itemModel->setOrbaallegroBidId($auctionTransaction->getBidId());
            }
			
			$productParentId	= $auction->getProductParentId();
			$buyRequest			= $auction->getBuyRequest();
			if ($productParentId && $buyRequest) {
				if (!empty($buyRequest) && !is_object($buyRequest)) {
					if (is_string($buyRequest)) {
						$buyRequest = unserialize($buyRequest);
					}
					if (is_array($buyRequest)) {
						$buyRequest = new Varien_Object($buyRequest);
					}
				}
				
				$product = Mage::getModel('catalog/product')->load($productParentId);
				$buyRequest->setQty($auctionTransaction->getQuantity());
				$itemModel = $this->getQuote()->addProductAdvanced(
							$product,
							$buyRequest,
							Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
						);
			}
            
            $this->getQuote()->addItem($itemModel);
        }
        
        return $this;
    }
    
    public function copyAddressDataIfEmpty($from, $to, $dataFields){
        $quote = $this->getQuote();
        if($from==Mage_Sales_Model_Quote_Address::TYPE_BILLING){
            $fromModel = $quote->getBillingAddress();
        }else{
            $fromModel = $quote->getShippingAddress();
        }
        if($to==Mage_Sales_Model_Quote_Address::TYPE_SHIPPING){
            $toModel = $quote->getShippingAddress();
        }else{
            $toModel = $quote->getBillingAddress();
        }
        /* @var $fromModel Mage_Sales_Model_Quote_Address */
        /* @var $toModel Mage_Sales_Model_Quote_Address */
        foreach($dataFields as $field){
            $data = $toModel->getDataUsingMethod($field);
            if(empty($data)){
                $sourceData = $fromModel->getDataUsingMethod($field);
                $toModel->setDataUsingMethod($field, $sourceData);
            }
        }
        return $this;
    }
    
    public function findCustomer($email, $websiteId) {
        $collection = Mage::getResourceModel('customer/customer_collection');
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection->addFieldToFilter("email", $email);
        $collection->addFieldToFilter("website_id", $websiteId);
        return $collection->getFirstItem();
    }
    
    /**
     * @param Orba_Allegro_Model_Auction_Bid $bid
     * @param array $otherBids - array(array("bid"=>bid1, "qty"=>qty1), ...);
     * @return Mage_Sales_Model_Quote
     */
    public function createFromBid(Orba_Allegro_Model_Auction_Bid $bid, $qty=1, $otherBids = array()) {
        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        
        $auction = $bid->getAuction();
        $store = $auction->getStore();
        $contractor = $bid->getContractor();
        $quote = $this->getQuote();
       
        $quote->addData(array(
            "store_id" => $store->getId(),
            "orbaallegro_allegro_buyer_id" => ($contractor && $contractor->getId()) ? 
                  $contractor->getAllegroUserId() : $bid->getAllegroBuyerId(),
            "orbaallegro_country_code" => $auction->getCountryCode(),
            "orbaallegro_contractor_id" => $bid->getContractorId(),
        ));
        
        $accountData = array();
        $billingAddress = array();
        $shippingAddress = array();
        
        /** Account **/
        if($contractor && $contractor->getId()){
            $accountData = array(
                "email" => $contractor->getEmail(),
                "firstname" => $contractor->getFirstname(),
                "lastname" => $contractor->getLastName()
            );
            
            /** Has account **/
            $customer = $this->findCustomer(
                    $contractor->getEmail(),
                    $store->getWebsiteId()
            );
            
            /** Registered User? **/
            if($customer && $customer->getId()){
                $quote->setCustomer($customer);
            }
            
            /** Addresses **/
            $billingAddress = $this->prepareContractorAddress($contractor);
            
            if((int)$contractor->getHasSendData()){
                $shippingAddress = $this->prepareContractorAddress($contractor, "send");
                $shippingAddress["same_as_billing"] = false;
            }else{
                $shippingAddress = $billingAddress;
                $shippingAddress["same_as_billing"] = true;
            }
            
        }else{
            $quote->setCustomerIsGuest(!$this->getCreateCustomer());
        }
        
        $this->importPostData(array(
            "account"           => $accountData,
            "billing_address"   => $billingAddress,
            "shipping_address"  => $shippingAddress,
        ));
        
        /** Item(s) **/
        $this->addItemFromBid($bid, $qty);        
        
        foreach($otherBids as $_bid){
            $this->addItemFromBid($_bid['bid'], isset($_bid['qty']) ? $_bid['qty'] : null);   
        }
        
        /** Collect shipping rates **/
        $quote->getShippingAddress()->requestShippingRates();
        
        return $quote;
    }
    
    public function prepareContractorAddress(Orba_Allegro_Model_Contractor $contractor, $prefix="") {
        $prefix = $prefix ? $prefix . "_" : "";
        return array(
            "firstname"=>   $contractor->getData($prefix."firstname"),
            "lastname"=>    $contractor->getData($prefix."lastname"),
            "company"=>     $contractor->getData($prefix."company"),
            "city"=>        $contractor->getData($prefix."city"),
            "street"=>      $contractor->getData($prefix."street"),
            "telephone"=>   $contractor->getData($prefix."phone"),
            "postcode"=>    $contractor->getData($prefix."postcode"),
            "country_id"=>  Mage::helper("orbaallegro")->getCountryMapped($contractor->getData($prefix."country")),
            "orbaallegro_allegro_buyer_id" => $contractor->getAllegroUserId(),
        );
    }
    
    public function addItemFromBid(Orba_Allegro_Model_Auction_Bid $bid, $qty=null) {
        $itemModel =  Mage::getModel("sales/quote_item");
        /* @var $itemModel Mage_Sales_Model_Quote_Item */

        $auction = $bid->getAuction();
        $product = $auction->getProduct();
        $store = $auction->getStore();
        
        // All prices includes tax
        $price = (float)$bid->getItemPrice();
        
        if(!is_numeric($qty)){
            $qty = max(array((float)$bid->getUnhandledItemCount(), 1));
        }

        $itemModel->setOrbaallegroAuctionId($auction->getId());
        $itemModel->setStoreId($store->getId());
        $itemModel->setQuote($this->getQuote());
        $itemModel->setName($auction->getAuctionTitle());
        $itemModel->setQty($qty);

        $setCustomPrice = true;
        if($product){
            $itemModel->setProduct($product);
            if((float)$product->getFinalPrice()==$price){
                $setCustomPrice = false;
            }
        }

        if($setCustomPrice){
            $itemModel->setCustomPrice($price);
            $itemModel->setOriginalCustomPrice($price);
        }

        $itemModel->setOrbaallegroBidId($bid->getId());

        $this->getQuote()->addItem($itemModel);
    }
    
    public function getOverrideRate($store) {
        return Mage::getSingleton('orbaallegro/config')->getOverrideRate($store->getId());
    }
    
    public function getCreateCustomer($store) {
        return Mage::getSingleton('orbaallegro/config')->getCreateCustomer($store->getId());
    }
}