<?php

class Orba_Allegro_Model_Transaction extends Mage_Core_Model_Abstract {

    
    
    protected $_transactionMap = array(
        // IDs
        "postBuyFormId"             => "allegro_transaction_id",
        "postBuyFormBuyerId"        => "allegro_buyer_id",
        "postBuyFormShipmentId"     => "allegro_shipment_id",
        
        // Money
        "postBuyFormPostageAmount"  => "transaction_cod_amount",
        "postBuyFormAmount"         => "transaction_total",
        
        // Buyer info
        "postBuyFormBuyerLogin"     => "buyer_login",
        "postBuyFormBuyerEmail"     => "buyer_email",
        "postBuyFormInvoiceOption"  => "buyer_invoice",
        "postBuyFormMsgToSeller"    => "buyer_message"
    );
    
    protected $_paymentMap = array(
        "postBuyFormPayId"          => "allegro_payu_transaction_id",
        "postBuyFormPayType"        => "allegro_payment_type",
        "postBuyFormPayStatus"      => "allegro_payu_status",
        "postBuyFormDateInit"       => "allegro_payu_start_date",
        "postBuyFormDateRecv"       => "allegro_payu_end_date",
        "postBuyFormDateCancel"     => "allegro_payu_cancel_date",
    );
    
    protected $_addressesTypes = array(
        "postBuyFormInvoiceData"    => Orba_Allegro_Model_Transaction_Address::TYPE_BILLING,
        "postBuyFormShipmentAddress"=> Orba_Allegro_Model_Transaction_Address::TYPE_SHIPPING,
        "postBuyFormGdAddress"      => Orba_Allegro_Model_Transaction_Address::TYPE_PICKPOINT
    );


    protected function _construct() {
        $this->_init('orbaallegro/transaction');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Transaction
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
    public function getSerializedData() {
        if(is_null($this->getData("serialized_data"))){
            $this->setData("serialized_data", 
                $this->getResource()->getSerializedData($this)
            );
        }
        return $this->getData("serialized_data");
    }
    
    
    /**
     * @return Mage_Sales_Model_Order|null
     */
    
    public function getOrder() {
        if($this->getOrderId()){
            $order = Mage::getModel("sales/order")->load($this->getOrderId());
            if($order->getId()){
                return $order;
            }
        }
        return null;
    }
    
    /**
     * @return Orba_Allegro_Model_Contractor
     */
    
    public function getContractor() {
        if($this->getContractorId()){
            $contractor = Mage::getModel("orbaallegro/contractor")->load($this->getContractorId());
            if($contractor->getId()){
                return $contractor;
            }
        }
        return null;
    }
    
    /**
     * @return boolean
     */
    public function getIsCarrierMapped() {
        if($this->getShipment()){
            return (bool)$this->getShipment()->getShipmentMap();
        }
        return false;
    }


    /**
     * @return Mage_Shipping_Model_Carrier_Interface
     */
    public function getCarrier() {
        if($shipment=$this->getShipment()){
            if($map=$shipment->getShipmentMap()){
                $map = explode("_", $map);
                $conf = Mage::getSingleton("shipping/config");
                /* @var $conf Mage_Shipping_Model_Config */
                return $conf->getCarrierInstance($map[0]);
            }
            return $this->_getDefaultCarrier();
        }
        return null;
    }
    
    public function getCarrierMethod() {
        
        $carrier=$this->getCarrier();
        $shipment=$this->getShipment();
                
        if($carrier && $shipment){
            $methodCode = "orbaallegro" . "_" . "method" . $shipment->getAllegroShipmentId();
            if($shipment->getShipmentMap()){
                $methodCode = $shipment->getShipmentMap();
            }
            
            $methods = array();
            
            try{
                $methods = $carrier->getAllowedMethods();
            }catch(Exception $e){
                
            }
            
            foreach($methods as $key=>$title) {
                /* @var $carrier Mage_Shipping_Model_Carrier_Abstract */
                if($carrier->getCarrierCode() . "_" . $key==$methodCode){
                    return new Varien_Object(array(
                        "carrier_code" => $carrier->getCarrierCode(),
                        "carrier_name" => $carrier->getConfigData("title"),
                        "code"    => $methodCode,
                        "title"   => $title
                    ));
                }
            }
        }
        
        return null;
    }
    
    /**
     * @return Orba_Allegro_Model_Mapping_Shipment_Carrier
     */
    protected function _getDefaultCarrier() {
        return Mage::getModel("orbaallegro/mapping_shipment_carrier");
    }
    
    /**
     * @return Orba_Allegro_Model_Mapping_Shipment|null
     */
    
    public function getShipment() {
        if($this->getShipmentId()){
            $shipment = Mage::getModel("orbaallegro/mapping_shipment")->load($this->getShipmentId());
            if($shipment->getId()){
                return $shipment;
            }
        }
        return null;
    }
    
    /**
     * @return Orba_Allegro_Model_Mapping_Payment
     */
    public function getPayment() {
        if(!$this->hasData("payment")){
            $payment = false;
            if($this->getData("payment_id")){
                $model = Mage::getModel("orbaallegro/mapping_payment");
                $model->load($this->getData("payment_id"));
                if($model->getId()){
                    $payment=$model;
                }
            }
            $this->setData("payment", $payment);
        }
        return $this->getData("payment");
    }
    
    /**
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getPaymentMethod() {
        if($this->isPaymentMapped()){
            foreach(Mage::getSingleton("payment/config")->getActiveMethods() as $key=>$method){
                if($key==$this->getPayment()->getPaymentMap()){
                    return $method;
                }
            }
            return null;
        }
        return Mage::getModel("orbaallegro/mapping_payment_method");
    }
    
    public function isPaymentMapped() {
        if($this->getPayment()){
            return array_key_exists(
                    $this->getPayment()->getPaymentMap(), 
                    Mage::getSingleton("payment/config")->getActiveMethods()/* getAllMethods */
            );
        }
        return false;
    }

    
    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Address_Collection | null
     */
    public function getAddressCollection() {
        if(!$this->hasData("address_collection")){
            if($this->getId()){
                $coll = Mage::getResourceModel('orbaallegro/transaction_address_collection');
                /* @var $coll Orba_Allegro_Model_Resource_Transaction_Address_Collection */
                $coll->addFieldToFilter('transaction_id', $this->getId());
                $this->setData("address_collection", $coll);
            }
        }
        return $this->getData("address_collection");
    }
    
    /**
     * @param string $type
     * @return Orba_Allegro_Model_Transaction_Address | null
     */
    public function getAddress($type=Orba_Allegro_Model_Transaction_Address::TYPE_BILLING) {
        if(($coll = $this->getAddressCollection())!==null){
            return $coll->getItemByColumnValue("type", $type);
        }
        return null;
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function getAuctionCollection() {
        if(!$this->hasData("auction_collection")){
            if($this->getId()){
                $aucitons = Mage::getResourceModel("orbaallegro/auction_collection");
                /* @var $auctions Orba_Allegro_Model_Resource_Auction_Collection */
                $aucitons->addTransactionFilter($this);
                $this->setData("auction_collection", $aucitons);
            }
        }
        return $this->getData("auction_collection");
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Auction_Collection
     */
    public function getTransactionAuctionCollection() {
        if(!$this->hasData("transaction_auction_collection")){
            if($this->getId()){
                $aucitons = Mage::getResourceModel("orbaallegro/transaction_auction_collection");
                /* @var $auctions Orba_Allegro_Model_Resource_Transaction_Auction_Collection */
                $aucitons->addFieldToFilter("transaction_id", $this->getId());
                $this->setData("transaction_auction_collection", $aucitons);
            }
        }
        return $this->getData("transaction_auction_collection");
    }
    
    /**
     * @return int
     */
    public function getSubtotal() {
        $cod = (float)$this->getData('transaction_cod_amount');
        $total = (float)$this->getData('transaction_amount');
        if($cod){
            return $total - $cod;
        }
        return 0;
    }
    
    /**
     * @return int
     */
    public function getItemCount() {
        return $this->getResource()->getItemCount($this);
    }
    
    /**
     * @return int
     */
    public function getAuctionCount() {
        return $this->getResource()->getAuctionCount($this);
    }
    
    /**
     * @param Orba_Allegro_Model_User $user
     * @return Orba_Allegro_Model_Transaction
     */
    public function bindUserData(Orba_Allegro_Model_User $user) {
        $this->setAllegroSellerId($user->getUserId());
        $this->setSellerLogin($user->getUserLogin());
        $this->setSellerEmail($user->getUserEmail());
        return $this;
    }
    
    /**
     * @param Orba_Allegro_Model_User $user
     * @return Orba_Allegro_Model_Transaction
     */
    public function bindServiceData(Orba_Allegro_Model_Service_Abstract $service) {
        $this->setCountryCode($service->getServiceCountryCode());
        $this->setCurrency($service->getServiceCurrency());
        return $this;
    }
    
    
    /**
     * @param stdClass $param
     * @return Orba_Allegro_Model_Transaction
     */
    public function bindAllegroTransactionData($data) {
        
        // Map always (payment status might change)
        $this->addData($this->_mapAllegroTransactionPaymentObject($data));
        
        // Map only for new objects
        if($this->getId()){
           return $this; 
        }
        
        // Base data
        $this->addData($this->_mapAllegroTransactionObject($data));
         
        // Subtotal
        $this->setTransactionSubtotal(
              $this->getTransactionTotal() - $this->getTransactionCodAmount() 
        );
        
        // Map shipment
        if($this->getAllegroShipmentId()){
            $shipment = $this->mapAllegroShipmentToLocalShipment(
                    $this->getAllegroShipmentId(),
                    $this->getCountryCode()
            );

            if($shipment){
                $this->setShipmentId($shipment->getId());
            }
        }

        // Map payment
        if($this->getAllegroPaymentType()){
            $payment = $this->mapAllegroPaymentToLocalPayment(
                    $this->getAllegroPaymentType(),
                    $this->getCountryCode()
            );
            if($payment){
                $this->setPaymentId($payment->getId());
            }
        }
        
        // Map contractor
        $contractor = null;
        if($this->getAllegroBuyerId()){
            $contractor = Mage::getModel("orbaallegro/contractor")->
                load($this->getAllegroBuyerId(), "allegro_user_id");
            /* @var $contractor Orba_Allegro_Model_Contractor */
            if($contractor->getId()){
                $this->setContractorId($contractor->getId());
            }
        }
        
        $allegroCreatedAt = null;
        
        // Map addresses
        $postAddresses = array();
        $hasBillingAddress = false;
        foreach($this->_addressesTypes as $key=>$type){
            if(isset($data->{$key}) && is_object($data->{$key})){
                $addressObj = $data->{$key};
                // Is no empty
                if($addressObj->postBuyFormAdrCountry!=0){
                    $addressModel = Mage::getModel("orbaallegro/transaction_address");
                    /* @var $addressModel Orba_Allegro_Model_Transaction_Address */
                    $addressModel->bindAllegroAddressData($addressObj);
                    $addressModel->setType($type);
                    
                    switch ($type) {
                        // Set has billing
                        case "billing":
                            $hasBillingAddress = true;
                        break;
                        // Add pickpoint info if exists
                        case "pickpoint":
                            if(isset($data->postBuyFormGdAdditionalInfo) && !empty($data->postBuyFormGdAdditionalInfo)){
                                $addressModel->setPickpointInfo($data->postBuyFormGdAdditionalInfo);
                            }
                        break;
                    }
                    
                    // Lookup for creation time
                    if($allegroCreatedAt===null && isset($addressObj->postBuyFormCreatedDate) && !empty($addressObj->postBuyFormCreatedDate)){
                        $allegroCreatedAt = $addressObj->postBuyFormCreatedDate;
                    }

                    
                    // Add addres to stack
                    $postAddresses[] = $addressModel;
                }
            }
        }
        
        if(!$hasBillingAddress){
            foreach($postAddresses as $addressModel){           
                /* @var $addressModel Orba_Allegro_Model_Transaction_Address */
                if($addressModel->getType()==Orba_Allegro_Model_Transaction_Address::TYPE_SHIPPING){
                    $billignAddress = clone $addressModel;
                    $billignAddress->setType(Orba_Allegro_Model_Transaction_Address::TYPE_BILLING);
                    $postAddresses[] = $billignAddress;
                    $addressModel->setSameAsBilling(1);
                    break;
                }
            }
        }
        $this->setData("post_addresses", $postAddresses);
        
        // Add allegro creation time 
        if($allegroCreatedAt){
            /* @todo check correc format in DB */
            $this->setAllegroCreatedAt($allegroCreatedAt);
        }
        
        // Add auction relations items
        $postItems = array();
        if(isset($data->postBuyFormItems)){
            if(!is_array($data->postBuyFormItems) && isset($data->postBuyFormItems->item)){
                $data->postBuyFormItems = $data->postBuyFormItems->item;
            }
            foreach($data->postBuyFormItems as $item){
                $itemModel = Mage::getModel("orbaallegro/transaction_auction");
                /* @var $itemModel Orba_Allegro_Model_Transaction_Auction */
                $itemModel->bindAllegroItemData($item);
				
				if(!$itemModel->getAuctionId()){
						Mage::log("Found not auciton id item for trans" . $this->getId());
						Mage::log($item);
						continue;
                }

                // Realted with bid?
                $auctionBid = Mage::getModel("orbaallegro/auction_bid")->
                    loadMulti(array(
                        "allegro_auction_id" => $item->postBuyFormItId,
                        "allegro_user_id" => $this->getAllegroBuyerId()
                    ));
                if($auctionBid->getId()){
                    $itemModel->setBidId($auctionBid->getId());
                }
                // Add to stack
                $postItems[] = $itemModel;
            }
        }
        $this->setData("post_items", $postItems);
        
        return $this;
    }
    
    /**
     * @param type $allegroId
     * @param type $countryCode
     * @return Orba_Allegro_Model_Mapping_Shipment
     */
    public function mapAllegroShipmentToLocalShipment($allegroId, $countryCode) {
        // Mapping shipment
        $shippingCollection = Mage::getResourceModel("orbaallegro/mapping_shipment_collection");
        /* @var $shippingCollection Orba_Allegro_Model_Resource_Mapping_Shipment_Collection */
        
        $shippingCollection
            ->addFieldToFilter("allegro_shipment_id", $allegroId)
            ->addFieldToFilter("country_code", $countryCode);
           
        if(($item=$shippingCollection->getFirstItem()) && $item->getId()){
            return $item;
        }
        
        return null;
    }
    
    /**
     * @param type $allegroId
     * @param type $countryCode
     * @return Orba_Allegro_Model_Mapping_Payment
     */
    public function mapAllegroPaymentToLocalPayment($payemntType, $countryCode) {
        $paymentCollection = Mage::getResourceModel("orbaallegro/mapping_payment_collection");
        /* @var $paymentCollection Orba_Allegro_Model_Resource_Mapping_Payment_Collection */
        $paymentCollection
            ->addFieldToFilter("allegro_payment_type", $payemntType)
            ->addFieldToFilter("country_code", $countryCode);
           
        if(($item=$paymentCollection->getFirstItem()) && $item->getId()){
            return $item;
        }
        
        return null;
    }
    
    /**
     * Save related stuff
     * @return Orba_Allegro_Model_Transaction
     */
    protected function _afterSave() {
        // Add addresses
        $addresses=$this->getData("post_addresses");
        if($addresses && is_array($addresses)){
            foreach($addresses as $address){
                /* @var $address Orba_Allegro_Model_Transaction_Address */
                $address->setTransactionId($this->getId());
                $address->save();
            }
            // Unset data (dont save in future)
            $this->setData("post_addresses", null);
        }
        
        // Add items
        $items=$this->getData("post_items");
        if($items && is_array($items)){
            foreach($items as $item){
                /* @var $item Orba_Allegro_Model_Transaction_Auction */
                $item->setTransactionId($this->getId());
				try{
					$item->save();
				}catch(Exception $e){
					Mage::log("Found exception");
					Mage::log($item->getData());
					Mage::logException($e);
				}
                // Unset data (dont save in future)
            }
            $this->setData("post_items", null);
        }
        return parent::_afterSave();
    }
    
    protected function _mapAllegroTransactionObject($data) {
        $out = array();
        foreach($data as $key=>$value){
            if(isset($this->_transactionMap[$key]) && !empty($value)){
                $out[$this->_transactionMap[$key]] = $value;
            }
        }
        return $out;
    }
    
    protected function _mapAllegroTransactionPaymentObject($data) {
        $out = array();
        foreach($data as $key=>$value){
            if(isset($this->_paymentMap[$key]) && !empty($value)){
                $out[$this->_paymentMap[$key]] = $value;
            }
        }
        return $out;
    }
    
    public function allegoCheckExists($save=true) {
        if($this->getAllegroTransactionId() && $this->getId()){
            $auction = $this->getAuctionCollection()->getFirstItem();
            /* @var $auction Orba_Allegro_Model_Auction */
            $stillExists = false;
            if($auction && $auction->getId()){
                $transaIds = $auction->allegroGetTransactionIds();
                if(in_array($this->getAllegroTransactionId(), $transaIds)){
                    $stillExists = true;
                }
            }
            
            $this->setIsDeleted((int)!$stillExists);
            
            if($save){
                $this->save();
            }
            
            return $stillExists;
        }
    }
    
    public function allegroSync() {
        if(!$this->getAllegroTransactionId() || !$this->getId() || !$this->getSellerLogin()){
            return;
        }
        
        $this->allegoCheckExists();
        
        if(!$this->getIsDeleted() || $this->getForceSync() || 1){
            $config = Mage::getSingleton("orbaallegro/config");
            /* @var $config Orba_Allegro_Model_Config */

            $store = $config->getStoreByLogin($this->getSellerLogin(), true);
            if(!$store){
                throw new Orba_Allegro_Exception("Wrong login data for:" , $this->getSellerLogin());
            }
            $storeId = $store->getId();

            $client = Mage::getModel("orbaallegro/client");
            /* @var $client Orba_Allegro_Model_Client */
            $client->setStoreId($storeId);

            // Step 1: get transaction details
            $requiredIds = array($this->getAllegroTransactionId()+0);

            $result = $client->getPostBuyFormsDataForSellers(array(
                Orba_Allegro_Model_Auction::KEY_GET_POST_BUY_FORMS_DATA_TRANSACTIONS_ID_ARRAY => $requiredIds
            ));
            // Step 2: process transaction
            if(!$result){
                return $this;
            }

            if(!is_array($result->postBuyFormData) && isset($result->postBuyFormData->item)){
                $result = $result->postBuyFormData->item;
            }else{
                $result = $result->postBuyFormData;
            }
            
            if(!is_array($result) || !count($result)){
                return $this;
            }

            $this->bindAllegroTransactionData($result[0]);
            $this->setSerializedData($result[0]);
            $this->save();
            $this->setSerializedData(null);

            // Update linked auctions
            foreach($this->getAuctionCollection() as $auction){
                /* @var $auction Orba_Allegro_Model_Auction */
                $auction->updateTransactionItemsSold();
            }
        }
        return $this;
    }
    
}