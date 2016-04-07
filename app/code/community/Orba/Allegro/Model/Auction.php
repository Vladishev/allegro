<?php

class Orba_Allegro_Model_Auction extends Mage_Core_Model_Abstract {

    const KEY_VERIFY_GET_POST_BUY_DATA_ITEMS = "itemsArray";
    const KEY_GET_BID_ITEM_ID = "itemId";
    
    const KEY_VERIFY_ITEM_LOCAL_ID = "localId";
    const KEY_CHANGE_FIELDS_ITEM_ID = "itemId";
    
    const KEY_GET_TRANSACTIONS_ID_ITEMS_ID_ARRAY = "itemsIdArray";
    const KEY_GET_TRANSACTIONS_ID_USER_ROLE = "userRole";
    
    const KEY_GET_ITEM_INFO_ITEM_ID = "itemId";
    const KEY_GET_POST_BUY_FORMS_DATA_TRANSACTIONS_ID_ARRAY = "transactionsIdsArray";
    
    protected $_closedStatuses = array(
        Orba_Allegro_Model_Auction_Status::STATUS_ENDED,
        Orba_Allegro_Model_Auction_Status::STATUS_FINISHED,
        Orba_Allegro_Model_Auction_Status::STATUS_CANCELED,
        Orba_Allegro_Model_Auction_Status::STATUS_IGNORED
    );


    protected function _construct() {
        $this->_init('orbaallegro/auction');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
    /**
     * @return Orba_Allegro_Model_Service_Abstract
     */
    public function getService() {
        $serviceFactory = Mage::getSingleton('orbaallegro/service');
        return $serviceFactory::factory($this->getCountryCode());
    }
    
    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore() {
        return Mage::getModel("core/store")->load($this->getStoreId());
    }
    
    /**
     * @return Orba_Allegro_Model_Template
     */
    public function getTemplate() {
        return Mage::getModel("orbaallegro/template")->load($this->getTemplateId());
    }
    
    /**
     * @return Orba_Allegro_Model_Category
     */
    public function getCategory() {
        return Mage::getModel("orbaallegro/category")->load($this->getCategoryId());
    }
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        return Mage::getModel("catalog/product")->load($this->getProductId());
    }

    

    public function getSerializedData() {
        return $this->getResource()->getSerializedData($this);
    }
    
    
    protected function _prepareToDuplicate() {
        $this->setParentId($this->getId());
        $this->setEndingAt(null);
        $this->setClosedAt(null);
        $this->setCreatedAt(null);
        $this->setUpdatedAt(null);
        $this->setHasOffers(0);
        $this->setItemsSold(0);
        $this->setItemsCanceled(0);
        $this->setAuctionCost(0);
        $this->setAllegroAuctionId(null);
        $this->setAllegroStartingTime(null);
        $this->setAllegroAuctionInfo(null);
		
		// Unset to renew flag
		// This flag is set after finish auction
		$this->_unregisterRenew();
		
        $this->setId(null);
    }
    
    /**
     * Verfify current auction - it's status
     * @return boolean
     */
    public function allegroVerify() {
        if(!$this->getId()){
            return false;
        }
        $storeId = $this->getStoreId();
        $localId = $this->getId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);
        
        // Step 1: Verify auction
        $result = false;
        try{
            $result = $client->verifyItem(array(
                self::KEY_VERIFY_ITEM_LOCAL_ID => (int)$localId
            ));
        }catch(Exception $e){
            Mage::logException($e);
            return false;
        }
        
        if($result && is_object($result)){
           
            $changed = false;
            
            if(!$this->getAllegroAuctionId() && isset($result->itemId)){
                // -1 - Allegro delay...
                if($result->itemId!=-1){
                    $this->setAllegroAuctionId($result->itemId);
                    $changed = true;
                }
            }
            
            if(isset($result->itemListed)){
                $statusCode = $result->itemListed;
                $model = Mage::getSingleton("orbaallegro/auction_status");
                /* @var $model Orba_Allegro_Model_Auction_Status */
                if($status=$model->getStatusByCode($statusCode)){
                    if($status!=$this->getAuctionStatus()){
                        $this->setAuctionStatus($status);
                        if(in_array($status, $this->_closedStatuses)){
                            $this->setClosedAt(Varien_Date::now());
                        }
                        $changed = true;
                    }
                }
            }
            
            if(isset($result->itemStartingTime)){
                $this->setAllegroStartingTime($result->itemStartingTime);
                $changed = true;
            }
            
            if($changed){
                $this->save();
            }
            return true;
        }    
        
        
        return false;
    }
    
    public function updateCostByAllegroResult($result, $save=true) {
        if($result && is_object($result) && isset($result->changedItem)){
            $result = $result->changedItem;
            if(isset($result->itemSurcharge) && isset($result->itemSurcharge->item)){
                $surCharge = $result->itemSurcharge->item;
                if(!is_array($surCharge)){
                    $surCharge = array($surCharge);
                }
                foreach ($surCharge as $item){
                    if($item->surchargeDescription==$this->_getSurchargeSumText()){
                        $cost = (float)$item->surchargeAmount->amountValue;
                        if($cost!=$this->getAuctionCost()){
                            $this->setAuctionCost($cost); // Cost (float)
                            $this->setAllegroAuctionInfo(sprintf(
                                    "%0.2f %s", $cost, $item->surchargeAmount->amountCurrencySign)); // Cost (string)
                            if($save){
                                $this->save();
                            }
                        }
                        break;
                    }
                }
                return true;
            }
        }
        return false;
    }
    
    protected function _getSurchargeSumText() {
        if(!$this->hasData("sum_text")){
            $this->setData("sum_text", $this->getService()->getSurchargeSumText());
        }
        return $this->getData("sum_text");
    }
    
    public function allegroGetAuctionCost() {
         if(!$this->getAllegroAuctionId()){
            return false;
        }
        $storeId = $this->getStoreId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);
        
        $result = false;
        try{
            $result = $client->checkChangeItemFields(array(
                self::KEY_CHANGE_FIELDS_ITEM_ID => $this->getAllegroAuctionId()
            ));
        }catch(Exception $e){
            Mage::logException($e);
            return false;
        }
        
        return $this->updateCostByAllegroResult($result);

    }

    public function allegroCheckStatus() {
        if(!$this->getId()){
            return false;
        }
        $storeId = $this->getStoreId();
        $localId = $this->getId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);
        $result = false;
        
        try{
            $result = $client->getItemInfo(array(
                   self::KEY_GET_ITEM_INFO_ITEM_ID => (float) $this->getAllegroAuctionId()
            ));
        }catch(Exception $e){
            Mage::logException($e);
            return false;
        }
        if($result){
            if(isset($result->itemListInfoExt)){
                $endingInfo = $result->itemListInfoExt->itEndingInfo;
                $endingTime = $result->itemListInfoExt->itEndingTime;
                $canceldInfo = 
                $model = Mage::getSingleton("orbaallegro/auction_status");
                /* @var $model Orba_Allegro_Model_Auction_Status */
                $changed = false;
                if($status=$model->getStatusByEndingInfo($endingInfo)){
                    if($status!=$this->getStatus()){
                        $this->setAuctionStatus($status);
						// Check is closed ?
                        if(in_array($status, $this->_closedStatuses)){
                            $this->setClosedAt($endingTime);
							// If finished normally process register renew
							if($status==Orba_Allegro_Model_Auction_Status::STATUS_FINISHED){
								$this->_registerRenew();
							// Else disable renew
							}else{
								$this->disableAutoRenew();
							}
                        }
						
                        $changed = true;
                    }
                }
                if($endingTime!=$this->getEndingAt()){
                    $this->setEndingAt($endingTime);
                    $changed = true;
                }
                if($changed){
                    $this->save();
                }
            }
            return true;
        }        
        return false;
    }

    /**
	 * FINISHED meens ENDED here!
	 * @param bool $cancelBids
	 * @param bool $reason
	 * @return boolean
	 * @throws Orba_Allegro_Exception
	 */
    public function allegroFinish($cancelBids=false, $reason=null){
        $auctionId = $this->getAllegroAuctionId();
        if (!$auctionId){
            $this->_updateStatusAfterFinish();
            return true;
        }
        
        if(!$this->getCanCancel()){
            return false;
        }
        
        if ($cancelBids && !$this->allegroHasBids()) {
            $cancelBids = false;
        }
        
        $data = array(
            "finishItemId" => $auctionId + 0,
            "finishCancelAllBids" => (int)$cancelBids,
            "finishCancelReason" => $reason
        );
        
        $client = Mage::getModel("orbaallegro/client")->addData(
                Mage::getSingleton("orbaallegro/config")->getLoginData($this->getStore())
        );
        /* @var $client Orba_Allegro_Model_Client */
        
        $result = $client->finishItem($data);
        
        if($result && is_object($result) && isset($result->finishValue)){
            if((int)$result->finishValue){
				// Unregister sell again
				$this->disableAutoRenew();
                // Set ended by user status
                $this->_updateStatusAfterFinish();
				
                return true;
            }else{
                throw new Orba_Allegro_Exception("Cannot finish auction (Allegro error)");
            }
        }
        
        return false;
    }
	
    protected function _updateStatusAfterFinish() {
        $this->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_ENDED)
                ->setClosedAt(Varien_Date::now())
                ->save();
    }


   /**
    * @return Orba_Allegro_Model_Resource_Transaction_Collection
    */
   public function getTransactionCollection() {
       return Mage::getResourceModel('orbaallegro/transaction_collection')->
            addAuctionFilter($this);
   }
   
   /**
    * @return Orba_Allegro_Model_Resource_Auction_Bid_Collection
    */
   public function getBidCollection() {
       $collection = Mage::getResourceModel("orbaallegro/auction_bid_collection");
       /* @var $collection Orba_Allegro_Model_Resource_Auction_Bid_Collection */
       $collection->addAuctionFilter($this);
       return $collection;
   }

   public function allegroGetTransactionIds() {
        if(!$this->getId() || !$this->getAllegroAuctionId()){
            return $this;
        }
        $storeId = $this->getStoreId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);
        $transactionArray = array($this->getAllegroAuctionId()+0);
        
        // Step 1: get auction linked transaction Ids
        $result = $client->getTransactionsIDs(array(
            self::KEY_GET_TRANSACTIONS_ID_ITEMS_ID_ARRAY=>$transactionArray,
            self::KEY_GET_TRANSACTIONS_ID_USER_ROLE=>"seller"
        ));
        
        if(!$result){
            return array();
        }
        
        if(!is_array($result->transactionsIdsArray) && isset($result->transactionsIdsArray->item)){
            $result->transactionsIdsArray = $result->transactionsIdsArray->item;
        }
        return $result->transactionsIdsArray;
        
   }
   
   /**
    * @return Orba_Allegro_Model_Auction
    * @throws Orba_Allegro_Exception
    * @throws Exception
    * @important Before trnsaction sync we have to sync contratcotrs & bids!
    */
   public function allegroSyncTransactions(&$newTransactionCount=null) {
        if(!$this->getId() || !$this->getAllegroAuctionId()){
            return $this;
        }
        $storeId = $this->getStoreId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);
        
        $requiredIds = $this->allegroGetTransactionIds();
        
        // Step 2: get transaction details
        
        if(!is_array($requiredIds) || !count($requiredIds)){
            // No transaction - mark all as deleted
            $this->_markTransactionAsDeleted();
            return true;
        }
        
        $result = $client->getPostBuyFormsDataForSellers(array(
            self::KEY_GET_POST_BUY_FORMS_DATA_TRANSACTIONS_ID_ARRAY => $requiredIds
        ));
        
        // Step 3: process transactions`
        if(!$result){
            return false;
        }
        
        if(!is_array($result->postBuyFormData) && isset($result->postBuyFormData->item)){
            $result = $result->postBuyFormData->item;
        }else{
            $result = $result->postBuyFormData;
        }
        // Load current user
        
        $user = Mage::getModel('orbaallegro/user', $this->getStoreId());
        /* @var $user Orba_Allegro_Model_User */
        
        $service = $this->getService();
        
        if(!$user->getUserId()){
            throw new Orba_Allegro_Exception("Cannot load Allegor user");
        }
        $existingIds = array();
        foreach($result as $transData){
            $allegroTransId = $transData->postBuyFormId;
            $model = Mage::getModel("orbaallegro/transaction")->
                    load($allegroTransId, "allegro_transaction_id");
            /* @var $model Orba_Allegro_Model_Transaction */
            
            if(!$model->getId()){
                $model->bindServiceData($service);
                $model->bindUserData($user);
                $newTransactionCount++;
            }
            
            $model->bindAllegroTransactionData($transData);
            $model->setSerializedData($transData);
            $model->setIsDeleted(0);
            $model->save();
            $model->setSerializedData(null);
            $existingIds[] = $allegroTransId;
        }
        
        $this->_markTransactionAsDeleted($existingIds);
        $this->updateTransactionItemsSold();
        
        return $this;
   }
   
   
   protected function _markTransactionAsDeleted(array $existingIds = array()) {
        // Set transaction as deleted if not currently exists in response
        $transColl = Mage::getResourceModel("orbaallegro/transaction_collection");
        /* @var $transColl Orba_Allegro_Model_Resource_Transaction_Collection */
        $transColl->addAuctionFilter($this, false);
        $transColl->addFieldToFilter("main_table.is_deleted", 0);
        $transColl->addFieldToFilter("main_table.allegro_transaction_id", array("nin"=>$existingIds));
        
        foreach($transColl as $transaction){
            $transaction->setIsDeleted(1);
            $transaction->save();
        }
   }


   public function allegroSyncAuctionContractors() {
        if(!$this->getId() || !$this->getAllegroAuctionId()){
            return $this;
        }
        $storeId = $this->getStoreId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);
        
        $result = $client->getPostBuyData(array(
            self::KEY_VERIFY_GET_POST_BUY_DATA_ITEMS => array($this->getAllegroAuctionId()+0
        )));
        
        if($result && isset($result->itemsPostBuyData)){
            if(isset($result->itemsPostBuyData->item)){
                $result->itemsPostBuyData = $result->itemsPostBuyData->item;
            }
            foreach($result->itemsPostBuyData as $postItems){
                if(isset($postItems->item)){
                    $postItems = $postItems->item;
                }
                
                if(isset($postItems->usersPostBuyData->item)){
                    $postItems->usersPostBuyData = $postItems->usersPostBuyData->item;
                }
                
                if(!isset($postItems->usersPostBuyData) || !is_array($postItems->usersPostBuyData)){
                    continue;
                }
				
                foreach($postItems->usersPostBuyData as $contractorData){
                    
                    $contractorId = $contractorData->userData->userId;
                    
                    $model = Mage::getModel("orbaallegro/contractor")->
                        load($contractorId, "allegro_user_id");
                    /* @var $model Orba_Allegro_Model_Contractor */
                    if(!$model->getId()){
                        $model->setAllegroUserId($contractorId);    
                    }
                    $model->bindAllegroData($contractorData);
                    $model->save();
                }
            }
        }
        
   }
   
   public function allegroSyncAuctionBids(&$newBidsCount=null) {
        if(!$this->getId() || !$this->getAllegroAuctionId()){
            return $this;
        }
        $storeId = $this->getStoreId();
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($storeId);

		try {
			$result = $client->getBidItem(array(
				self::KEY_GET_BID_ITEM_ID => $this->getAllegroAuctionId()+0
			));

			if($result && isset($result->biditemList)){
				if(isset($result->biditemList->item)){
					$result->biditemList = $result->biditemList->item;
				}
				foreach($result->biditemList as $bidItem){
					if(isset($bidItem->bidsArray)){
						$bidItem = $bidItem->bidsArray;
					}
					if(isset($bidItem->item)){
						$bidItem = $bidItem->item;
					}
					$model = Mage::getModel("orbaallegro/auction_bid");
					/* @var $model Orba_Allegro_Model_Auction_Bid */
					$model->loadMulti(array(
						"allegro_auction_id"    => $bidItem[0], 
						"allegro_user_id"       => $bidItem[1]
					));
					$model->bindAllegroData($bidItem);
					// New Model
					if(!$model->getId()){
						$model->setSellerLogin($client->getLogin());
						$newBidsCount++;
					// New items
					}elseif($model->getItemQunatity()>$model->getOrigData('item_qunatity')){
						$newBidsCount++;
					}
					$model->save();
				}
				$this->updateItemsSold();
			}
		} catch (Exception $ex) {
			//Allegro Exception Like: ERR_AUCTION_KILLED: Wskazana oferta została usunięta przez administratora serwisu.
			Mage::logException($this->getAllegroAuctionId() . ' => ' . $ex);
			$this->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_CANCELED);
			$this->save();
		}
   }
   
   /**
    * Duplicate action localy and save to Allegro
    * Its simillar to save again, but can changeitem qty & more (todo)
    * @return bool
    * @throws Orba_Allegro_Exception
    */
   public function allegroRenewAuction() {
	   
	   
		
        if(!$this->getStore()->getId() || !$this->getProduct()->getId() || !$this->getCategory()){
            throw new Orba_Allegro_Exception("No store, product or category data");
        }
        
        $service = $this->getService();
        
        if(!$service->getId()){
            throw new Orba_Allegro_Exception("No service");
        }
		
		$newQty = $this->getItemsPlaced();
		
		if($newQty<1){
			throw new Orba_Allegro_Exception("Zero-count qty for auction");
		}
		
		$parsedData = $this->getSerializedData();
        $parserOut = Mage::getModel("orbaallegro/form_parser_out", $service);
		
		
		// Set items palced by registeed before item qty
	    $this->_prepareToDuplicate();
		
		$this->setItemsPlaced($newQty);
		
		$this->getResource()->beginTransaction();
		
		try{

			$client = Mage::getModel("orbaallegro/client");
			/* @var $client Orba_Allegro_Model_Client */
			$client->setStoreId($this->getStoreId());

			// Set status - only localy saved & prerocess some values
			$this->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_LOCALY);


			// 1. Step: Process serialized data with new data & Save auction locally ang get Id
			if(isset($parsedData[$parserOut::KEY_FIELDS]) && is_array($parsedData[$parserOut::KEY_FIELDS])){
				foreach($parsedData[$parserOut::KEY_FIELDS] as $key=>$field){
					if($field['fid']==$service::ID_QUANTITY){
						$parsedData[$parserOut::KEY_FIELDS][$key]['fvalueInt'] = $this->getItemsPlaced();
					}
				}
			}
			
			$this->setSerializedData($parsedData);
			$this->save();
			$this->setData('serialized_data', null);

			$auctionId = $this->getId();
			$parsedData[Orba_Allegro_Model_Form_Parser_Out::KEY_LOCAL_ID] = $auctionId;

			// 2. Save auction in allego
			$result = $client->newAuctionExt($parsedData);

			// 3. Step: Save extanal data to local table & change status to not vefificed
			if(!$result){
				throw new Orba_Allegro_Exception("Empty result");
			}

			$externalId = null;
			if(isset($result->itemId)){
				$externalId = $result->itemId;
			}
			$auctionInfo = "";
			if(isset($result->itemInfo)){
				$auctionInfo = $result->itemInfo;    
			}
			$cost = explode(" ", $auctionInfo);
			$this->setAllegroAuctionId($externalId);
			$this->setAllegroAuctionInfo($auctionInfo);
			if(count($cost) && !empty($cost[0])){
				$this->setAuctionCost((float)  str_replace(",", ".", $cost[0]));
			}
			$this->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_NO_VERIFIED);
			$this->save();

			// 4. Step: Verfiy auction
			$this->allegroVerify();

			// 5. Step: Get auction costs
			$this->allegroGetAuctionCost();
			
			// 6. Commit transaction
			$this->getResource()->commit();
		} catch (Exception $ex) {
			$this->getResource()->rollBack();
			Mage::logException($ex);
			return false;
		}

		return true;
	   
   }
   
   public function allegroSellAgain($durationDays, $startingTimestamp=null) {
       
        $service =  $this->getService();
        $parserOut = Mage::getModel("orbaallegro/form_parser_out", $service);
        
        $mapModel = Mage::getModel("orbaallegro/system_config_source_attribute_template_duration");
        /* @var $mapModel Orba_Allegro_Model_System_Config_Source_Attribute_Template_Duration */
        
        $map = array_flip($mapModel->toOptionHashForModify());
        
        if(isset($map[$durationDays])){
            $durationFormValue = $map[$durationDays];
        }else{
            $durationDays = $mapModel::DAYS_7;
            $durationFormValue = $map[$durationDays];
        }

        // Step 1: Clone data, clear allegro-depended data, 
        $origAllegroId = $this->getAllegroAuctionId();
        $serializedData = $this->getSerializedData();
        $this->_prepareToDuplicate();
        
        // Step 2: Change serialized data
        if(isset($serializedData[$parserOut::KEY_FIELDS])){
            $toChange = array(
                $service::ID_DURATION => $durationFormValue
            );
            
            if($startingTimestamp){
                $startingTimeObject = new Zend_Date();
                $startingTimeObject->setTimestamp($startingTimestamp);
                $now = new Zend_Date();
                if($startingTimeObject->compare($now)>-1){
                    $toChange[$service::ID_STARTING_TIME] = $startingTimeObject->getTimestamp();
                }
            }
            
            // Clear old values
            foreach($serializedData[$parserOut::KEY_FIELDS] as $key=>$field){
                if(array_key_exists($field[$parserOut::KEY_FID], $toChange)){
                    unset($serializedData[$parserOut::KEY_FIELDS][$key]);
                }
            }
            
            // Push new values
            if(count($toChange)){
                foreach($toChange as $fid=>$value){
                    $serializedData[$parserOut::KEY_FIELDS][] = array(
                        $parserOut::KEY_FID => $fid,
                        $parserOut::KEY_FVALUE_INT => $value
                    );
                }
            }
        }
        // Step 3: Add additional parmas & save
        $this->setSerializedData($serializedData);
        $this->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_LOCALY);
        //$this->setAllegroStartingTime($startingTimestamp); // get by verify method
        $this->setAuctionDuration($durationDays);
        $this->save();
        $this->setData('serialized_data', null); // Clear serialized data - do not save again
        
        // Step 4: Get local Id & save to Allegro
        $data = array(
            "sellItemsArray"            => array($origAllegroId+0),
            "sellAuctionDuration"       => (int)$durationDays,
            "localIds"                  => array((int)$this->getId())
        );
        if($startingTimestamp){
            $data["sellStartingTime"]   = (int)$startingTimestamp;
        }
        
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($this->getStore());
        
        $result = $client->sellAgain($data);
        
        $throwInfo = "Empty result";
        // Step 5: Save Allegro data 
        if($result && is_object($result) && isset($result->itemsSellAgain)){
            if(isset($result->itemsSellAgain->item)){
                if(!is_array($result->itemsSellAgain->item)){
                    $result->itemsSellAgain->item = array($result->itemsSellAgain->item);
                }
                foreach($result->itemsSellAgain->item  as $item){
                    if(isset($item->sellItemLocalId) && $item->sellItemLocalId==$this->getId()){
                        $this->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_NO_VERIFIED);
                        $this->setAuctionCost($this->_extractPriceFromString($item->sellItemInfo));
                        $this->setAllegroAuctionInfo($item->sellItemInfo);
                        // Auction saved correctly. Save local & return;
                        return $this->save();
                    }
                }
            }
            $throwInfo = "Source auction invaild or not found";
        }
        
        $this->delete();
        throw new Orba_Allegro_Exception(Mage::helper('orbaallegro')->__($throwInfo)); 
   }

   protected function _extractPriceFromString($str) {
       if(preg_match("/(\d(\d|\s)*\s*[\,\.]{1}\s*\d*)/", $str, $matches)){
           $float = str_replace(" ", "", $matches[1]);
           return floatval(str_replace(',', '.', str_replace('.', '', $float)));
       }
       return null;
   }



   public function updateItemsSold() {
       $this->getResource()->updateItemsSold($this);
       return $this;
   }
   
   public function updateTransactionItemsSold() {
       $this->getResource()->updateTransactionItemsSold($this);
       return $this;
   }
   
   public function getCanCancel() {
       return in_array($this->getAuctionStatus(), array(
            Orba_Allegro_Model_Auction_Status::STATUS_PLACED,
            Orba_Allegro_Model_Auction_Status::STATUS_INCOMING
            /*Orba_Allegro_Model_Auction_Status::STATUS_SELL_AGIAN*/
       ));
   }
   
   public function getCanModify() {
       return in_array($this->getAuctionStatus(), array(
            Orba_Allegro_Model_Auction_Status::STATUS_PLACED,
            /*Orba_Allegro_Model_Auction_Status::STATUS_NO_VERIFIED,
            Orba_Allegro_Model_Auction_Status::STATUS_INCOMING,
            Orba_Allegro_Model_Auction_Status::STATUS_SELL_AGIAN,
            Orba_Allegro_Model_Auction_Status::STATUS_WAITING,*/
       ));
   }
   
   public function getCanSellAgain() {
       return in_array($this->getAuctionStatus(), array(
            Orba_Allegro_Model_Auction_Status::STATUS_ENDED,
            Orba_Allegro_Model_Auction_Status::STATUS_FINISHED,
            Orba_Allegro_Model_Auction_Status::STATUS_IGNORED,
            Orba_Allegro_Model_Auction_Status::STATUS_CANCELED,
       ));
   }
   
   public function getWasPlaced() {
       return in_array($this->getAuctionStatus(), array(
            Orba_Allegro_Model_Auction_Status::STATUS_PLACED,
            Orba_Allegro_Model_Auction_Status::STATUS_ENDED,
            Orba_Allegro_Model_Auction_Status::STATUS_FINISHED,
            Orba_Allegro_Model_Auction_Status::STATUS_CANCELED
       ));
   }
   
   public function isOnline() {
        return in_array($this->getAuctionStatus(), array(
            Orba_Allegro_Model_Auction_Status::STATUS_PLACED
        ));
   }
   
   public function getAuctionLink() {
       return $this->getService()->getAuctionLink($this->getAllegroAuctionId());
   }
   
   public function getAllegroStartingTimeObject() {
       if($this->getAllegroStartingTime()){
            $date = new Zend_Date();
            $date->setTimestamp($this->getAllegroStartingTime());
            return $date;
       }
       return null;
   }
   
   public function getEndingTime() {
       if($this->getEndingAt()){
           $date = new Zend_Date();
           $endingAt = $this->getEndingAt();
           if(is_numeric($endingAt)){
               $date->setTimestamp($endingAt);
           }else{
               $date->setDate($endingAt, Varien_Date::DATETIME_INTERNAL_FORMAT);
           }
           return $date;
       }
       if($date = $this->getAllegroStartingTimeObject()){
            $date->addDay($this->getAuctionDuration());
            return $date;
       }
       return null;
   }
   
   public function isFinishedByUser() {
       return $this->getAuctionStatus() == Orba_Allegro_Model_Auction_Status::STATUS_ENDED;
   }
   
   public function getOfferCount($status=null) {
       return $this->getResource()->getOfferCount($this, $status);
   }
   
   /**
    * @return Mage_Directory_Model_Currency
    */
   public function getCurrencyModel() {
       return  Mage::getModel('directory/currency')->load($this->getCurrency());
   }
   
   public function getActiveItemsSold() {
       return $this->getResource()->getActiveItemsSold($this);
   }
   
   public function getIgnoredItemsSold() {
       return $this->getResource()->getIgnoredItemsSold($this);
   }
   
   /**
    * Resgister as auction to auto renew
    * renew is processed by cron
    * @param bool $save
    */
   protected function _registerRenew($save=false) {
	  
	   // Calculate items
	   $itemsToRenew = 0;
	   
	   switch ($this->getRenewType()) {
		   case Orba_Allegro_Model_System_Config_Source_Attribute_Template_Autorenew::RENEW_COMPLETE:
				$itemsToRenew = $this->getItemsPlaced();
		   break;
		   
		   case Orba_Allegro_Model_System_Config_Source_Attribute_Template_Autorenew::RENEW_NOT_SOLD:
			   $itemsToRenew = max(0, $this->getItemsPlaced()-$this->getItemsSold());
		   break;
	   }
	   
	   $this->setRenewItems($itemsToRenew);
	   
	   if($itemsToRenew>0){
			$this->setDoRenew(1);
	   }
	   
	   if($save){
		   $this->save();
	   }
   }
   
   /**
    * Unregister as auction to sell again
    * @param bool $save
    */
   protected function _unregisterRenew($save=false) {
	   $this->setDoRenew(0);
	   $this->setRenewItems(null);
	   if($save){
		   $this->save();
	   }
   }
   
   	/**
	 * Disable auto renew - clear renew flag and renew_type
	 * @return Orba_Allegro_Model_Auction
	 */
	public function disableAutoRenew() {
		// Unregister auto renew if exists
		$this->_unregisterRenew();
		// Set renew type to not ewne
		$notSellValue = Orba_Allegro_Model_System_Config_Source_Attribute_Template_Autorenew::DO_NOT_RENEW;
		$this->setRenewType($notSellValue);
		// Check serialized data
		$data = $this->getSerializedData();
		$service = $this->getService();
		$parserOut = Mage::getModel("orbaallegro/form_parser_out", $service);
		foreach($data[$parserOut::KEY_FIELDS] as &$field){
			if($field['fid']==$service::ID_AUTO_RENEW && $field[$parserOut::KEY_FVALUE_INT]!=$notSellValue){
				$field[$parserOut::KEY_FVALUE_INT] = $notSellValue;
				$this->setSerializedData($data);
				break;
			}
		}
		return $this;
	}
    
    /**
     * Checks if auction has any bids.
     * @return boolean
     */
    public function allegroHasBids() {
        $this->allegroCheckStatus();
        $this->allegroSyncAuctionContractors();
        $this->allegroSyncAuctionBids();
        $this->allegroSyncTransactions();
        return $this->getBidCollection()->getSize() > 0;
    }
    
}