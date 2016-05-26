<?php

class Orba_Allegro_Adminhtml_AuctionController 
    extends Orba_Allegro_Controller_Adminhtml_Abstract {

    public function indexAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('Auctions'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {	
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_registerObjects();
        
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('New Auction'));
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        $this->renderLayout();
    }

    public function viewAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_registerObjects();
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('View Auction'));
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        $this->renderLayout();
    }

	/**
	 * @todo use allegroNewAuction
	 * @return type
	 * @throws Orba_Allegro_Exception
	 */
    public function saveNewAction() {
        
        $this->_registerObjects();
        
        if(!$this->_getStore()->getId() || !$this->_getProduct()->getId() || !$this->_getCategory()){
            $this->_getSession()->addError("No store, product or category data");
            return $this->_redirectReferer();
        }
        
        if(!is_null($msg=$this->_stopProcess(true, $this->_getStore()->getId()))){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        // Prepare data
        
        $data = $this->getRequest()->getParams();
        $auctionData = $data["auction"];
        
        $service = Orba_Allegro_Model_Service::factory($this->_getCountryCode());
        
        if(!$service->getId()){
            $this->_getSession()->addError("No service");
            return $this->_redirectReferer();
        }
        
        try{
            $parserOut = Mage::getModel("orbaallegro/form_parser_out", $service);
            /* @var $parserOut Orba_Allegro_Model_Form_Parser_Out */

            $auction = Mage::getModel("orbaallegro/auction");
            /* @var $auction Orba_Allegro_Model_Auction */
            $config = $this->_getConfig();
            
            $parsedData = $parserOut->parse($auctionData);
			
			$productId = $this->_getProduct()->getId();
			
			if ($this->_getParentProductId()) {
				$productId = $this->_getSimpleProductId();
			}
			
            // Set base data
            $auction->setData(array(
                 "country_code"         => $this->_getCountryCode(),
                 "template_id"          => $this->_getTemplate()->getId(),
                 "product_id"           => $productId,
                 "category_id"          => $this->_getCategory()->getId(),
                 "store_id"             => $this->_getStore()->getId(),
                 "allegro_seller_id"    => $this->_getUser()->getUserId(),
                 "seller_login"         => $this->_getUser()->getUserLogin(),
                 "seller_email"         => $this->_getUser()->getUserEmail(),
                 "currency"             => $this->_getService()->getServiceCurrency(),
                 "product_parent_id"	=> $this->_getParentProductId(),
                 "buy_request"			=> $this->_getBuyRequest()
            ));
            
 
            
            // Set serilized data
            $auction->setSerializedData($parsedData);
            
            // Assing From data
            $this->_assingFormDataToAuction($auction, $auctionData);
            
            // Set status - only localy saved
            $auction->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_LOCALY);
			
            // 1. Step: Save auction locally ang get Id
            $auction->save();
            $auction->setData('serialized_data', null); // Do not process serilized more
            
            $auctionId = $auction->getId();
            
            // 2. Step: Save auction to Allegro with local Id
            $result = $this->_newAuction($parsedData, $auctionId);
            
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
            $auction->setAllegroAuctionId($externalId);
            $auction->setAllegroAuctionInfo($auctionInfo);
            if(count($cost) && !empty($cost[0])){
                $auction->setAuctionCost((float)  str_replace(",", ".", $cost[0]));
            }
            $auction->setAuctionStatus(Orba_Allegro_Model_Auction_Status::STATUS_NO_VERIFIED);
            $auction->save();
            
            // 4. Step: Verfiy auction
            $auction->allegroVerify();
            
            // 5. Step: Get auction costs
            $auction->allegroGetAuctionCost();
            
            $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->
                    __("Auction created. Status will change immediately. You can refresh statuses manually."));
            
            return $this->_redirect("*/*/index");
            
        }catch(Orba_Allegro_Model_Client_Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        }catch(Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
    }
    
    public function saveAction() {
               
        $this->_registerObjects();
        
        if(!$this->_getStore()->getId() || !$this->_getProduct()->getId() || !$this->_getCategory()){
            $this->_getSession()->addError("No store, product or category data");
            return $this->_redirectReferer();
        }
        
        if(!is_null($msg=$this->_stopProcess(true, $this->_getStore()->getId()))){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        // Prepare data
        
        $data = $this->getRequest()->getParams();
        $auctionData = $data["auction"];
        $service = $this->_getService();
        
        try{
            $parserOut = Mage::getModel("orbaallegro/form_parser_out", $service);
            /* @var $parserOut Orba_Allegro_Model_Form_Parser_Out */

            $auction = $this->_getAuction();
            /* @var $auction Orba_Allegro_Model_Auction */
            
            $config = $this->_getConfig();
            $mergedData = array();
            
            $parsedData = $parserOut->parseAndCompare($auctionData, $this->_getAuction(), $mergedData);
            
           
            $result = $this->_editAuction($parsedData);
            
            if(!$result || !is_object($result) || !$result->changedItem){
                throw new Orba_Allegro_Exception("Empty result");
            }
            
            // Save updated
            $auction->updateCostByAllegroResult($result, false);

            // Set serilized data
            $auction->setSerializedData($mergedData);
            
            // Add post data
            $this->_assingFormDataToAuction($auction, $auctionData);
            $auction->save();
            
            $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->
                    __("Auction changed"));

            return $this->_redirect("*/*/index");
            
        }catch(Orba_Allegro_Model_Client_Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        }catch(Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
    }
    
    public function sellAgainAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_registerObjects();
        
        $auction = $this->_getAuction();
        $service = $this->_getService();
        $request = $this->getRequest();
        
        // Setup duration
        $durationDays = $auction->getAuctionDuration();
        
        // Setup staring time
        $startingTimestamp = null;
        
        if($startingTime=$request->getParam("starting_time")){
            $startingTime = new Zend_Date($startingTime);
            $now = new Zend_Date();
            if($startingTime->compare($now)>-1){
                $startingTimestamp = $startingTime->getTimestamp();
            }
        }
        
        
        try{
            // Save to allegro
            $auction->allegroSellAgain($durationDays, $startingTimestamp);
            // Verify new placed auction
            $auction->allegroVerify();
            
            $this->_getSession()->addSuccess(
                    Mage::helper('orbaallegro')->__("Auction duplicated. Status will change immediately.")
            );
            // Return to created auciton
            return $this->_redirect("*/*/view", array("auction_id"=>$auction->getId()));
            
        }catch(Orba_Allegro_Model_Client_Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        }catch(Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
        
        
    }
    
    public function refreshAction() {
        $model = Mage::getSingleton("orbaallegro/observer");
        /* @var $model Orba_Allegro_Model_Observer */
        
        try{
            $model->verifyAucitons();
            $model->checkPlacedAuctions();
        }catch(Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirectReferer();
        }
        return $this->_redirectReferer();
    }
    
    /**
     * Test new auction
     */
    public function testNewAction() {
		
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        
        $this->_registerObjects();
        
        $data = $this->getRequest()->getParams();
        $auctionData = $data["auction"];
        $service = Orba_Allegro_Model_Service::factory($this->_getCountryCode());
       
        if($service->getId()){
            $parser = Mage::getModel("orbaallegro/form_parser_out", $service);
            /* @var $parser Orba_Allegro_Model_Form_Parser_Out */
            $parsedData = $parser->parse($auctionData);
            
            $error = false;
            $result = "";
            
            try{
                $result = $this->_checkNewAcution($parsedData);
            }catch(Exception $e){
                $error = $e->getMessage();
            }
            
            $block = Mage::getSingleton("core/layout")->
                    createBlock("core/template")->
                    setTemplate("orbaallegro/auction/edit/test_new.phtml")->
                    setError(Zend_Json::encode($error))->
                    setMakeSave($this->getRequest()->getParam("make_save"))->
                    setResult(Zend_Json::encode($result));
            
            $this->getResponse()->setBody($block->toHtml());
            
        }
        
    }
    
    /**
     * Test existing auction
     */
    public function testAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        
        $this->_registerObjects();
        
        $data = $this->getRequest()->getParams();
        $auctionData = $data["auction"];
        $service = Orba_Allegro_Model_Service::factory($this->_getCountryCode());
       
        if($service->getId()){
            $parser = Mage::getModel("orbaallegro/form_parser_out", $service);
            
            $mergedData = array();
            
            /* @var $parser Orba_Allegro_Model_Form_Parser_Out */
            $parsedData = $parser->parseAndCompare($auctionData, $this->_getAuction(), $mergedData);
            
            
            $error = false;
            $result = "";
            
            try{
                $result = $this->_checkEditAuction($parsedData);
            }catch(Exception $e){
                $error = $e->getMessage();
            }
            
            $block = Mage::getSingleton("core/layout")->
                    createBlock("core/template")->
                    setTemplate("orbaallegro/auction/edit/test.phtml")->
                    setError(Zend_Json::encode($error))->
                    setMakeSave($this->getRequest()->getParam("make_save"))->
                    setResult(Zend_Json::encode($result));
            $this->getResponse()->setBody($block->toHtml());
        }
        
    }

    public function cancelAction(){
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_registerObjects();
        
        $cancleBids = (int)$this->getRequest()->getParam("cancel_bids",0);
        $reason = $this->getRequest()->getParam("cancel_reason");
        
        try{
           $this->_getAuction()->allegroFinish($cancleBids, $reason);
        }catch(Orba_Allegro_Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        }catch(Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Other error (check logs)"));
            Mage::logException($e);
            return $this->_redirectReferer();
        }
        
        $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__("Auction finished"));
        return $this->_redirectReferer();
    }
    
    public function refreshTransactionsAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_registerObjects();
        
        $newTransactionCount = 0;
        try{
           $this->_getAuction()->allegroSyncAuctionContractors();
           $this->_getAuction()->allegroSyncAuctionBids($newBidCount);
           $this->_getAuction()->allegroSyncTransactions($newTransactionCount);
        }catch(Orba_Allegro_Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        }catch(Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Other error (check logs) $e"));
            Mage::logException($e);
            return $this->_redirectReferer();
        }
        
        $messages = array();
        
        if($newBidCount>0){
            $messages[] = Mage::helper('orbaallegro')->__("There are %s new bid(s)", $newBidCount);
        }
        if($newTransactionCount>0){
            $messages[] = Mage::helper('orbaallegro')->__("There are %s new transaction(s)", $newTransactionCount);
        }
        
        if(!count($messages)){
            $messages[] = Mage::helper('orbaallegro')->__("There are no new transactions");
        }
        
        foreach ($messages as $message){
            $this->_getSession()->addSuccess($message);
        }
        
        return $this->_redirectReferer();
    }
    
    public function gridAction() {
        $this->_registerObjects();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function gridTransactionAction() {
        $this->_registerObjects();
        $this->loadLayout();
        $this->renderLayout();
    }
    public function gridBidAction() {
        $this->_registerObjects();
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function massFinishAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
        } else {
            $auctionIds = $this->getRequest()->getParam('auction_id');
            if (empty($auctionIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orbaallegro')->__('Please select auction(s).'));
            } else {
                $cancelBids = (int) $this->getRequest()->getParam('cancel_bids', 0);
                $reason = null;
                $redirect = false;
                if ($cancelBids) {
                    $reason = $this->getRequest()->getParam("cancel_reason", '');
                    if (empty($reason)) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orbaallegro')->__('You have to specify cancellation reason.'));
                        $redirect = true;
                    }
                }
                if (!$redirect) {
                    $result = $this->_massFinish($auctionIds, $cancelBids, $reason);
                    if (!empty($result['success'])) {
                        $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__("Auctions finished") . ': ' . count($result['success']));
                        $auctionIds = array_diff($auctionIds, $result['success']);
                    }
                    if (!empty($result['error'])) {
                        $message = Mage::helper('orbaallegro')->__('Finishing failed for auctions') . ': ';
                        $errors = array();
                        foreach ($result['error'] as $error) {
                            $errors[] = $error['id'] . ' (' . $error['message'] . ')';
                        }
                        $message .= implode(', ', $errors);
                        Mage::getSingleton('adminhtml/session')->addError($message);
                    }
                    return $this->_redirect("*/*/index", empty($auctionIds) ? array() : array('filter' => 'bWFzc2FjdGlvbj0xJmVuZF90aW1lJTVCbG9jYWxlJTVEPXBsX1BM', 'internal_auction_id' => implode(',', $auctionIds)));
                }
            }
        }
        return $this->_redirect("*/*/index");
    }
    
    protected function _massFinish($auctionIds, $cancelBids, $reason) {
        $auctions = Mage::getModel('orbaallegro/auction')->getCollection()
                ->addFieldToFilter('auction_id', $auctionIds);
        $result = array(
            'success' => array(),
            'error' => array()
        );
        foreach ($auctions as $auction) {
            try {
                if ($auction->allegroFinish($cancelBids, $reason)) {
                    $result['success'][] = $auction->getId();
                } else {
                    throw new Orba_Allegro_Exception(Mage::helper('orbaallegro')->__("Cannot cancel auction"));
                }
            } catch(Orba_Allegro_Exception $e){
                $result['error'][] = array(
                    'id' => $auction->getId(),
                    'message' => $e->getMessage()
                );
            } catch (Exception $e){
                $result['error'][] = array(
                    'id' => $auction->getId(),
                    'message' => Mage::helper('orbaallegro')->__("Other error (check logs)")
                );
                Mage::logException($e);
            }
        }
        return $result;
    }
    
    public function massSellAgainAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
        } else {
            $auctionIds = $this->getRequest()->getParam('auction_id');
            if (empty($auctionIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orbaallegro')->__('Please select auction(s).'));
            } else {
                $service = $this->_getService();
                $request = $this->getRequest();

                // Setup staring time
                $startingTimestamp = null;

                if($startingTime=$request->getParam("starting_time")){
                    $startingTime = new Zend_Date($startingTime);
                    $now = new Zend_Date();
                    if($startingTime->compare($now)>-1){
                        $startingTimestamp = $startingTime->getTimestamp();
                    }
                }

                $result = $this->_massSellAgain($auctionIds, $startingTimestamp);
                if (!empty($result['success'])) {
                    $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__("Auctions sold again") . ': ' . count($result['success']));
                    $auctionIds = array_diff($auctionIds, $result['success']);
                }
                if (!empty($result['error'])) {
                    $message = Mage::helper('orbaallegro')->__('Selling again failed for auctions') . ': ';
                    $errors = array();
                    foreach ($result['error'] as $error) {
                        $errors[] = $error['id'] . ' (' . $error['message'] . ')';
                    }
                    $message .= implode(', ', $errors);
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                return $this->_redirect("*/*/index", empty($auctionIds) ? array() : array('filter' => 'bWFzc2FjdGlvbj0xJmVuZF90aW1lJTVCbG9jYWxlJTVEPXBsX1BM', 'internal_auction_id' => implode(',', $auctionIds)));
            }
        }
        return $this->_redirect("*/*/index");
    }
    
    protected function _massSellAgain($auctionIds, $startingTimestamp) {
        $auctions = Mage::getModel('orbaallegro/auction')->getCollection()
                ->addFieldToFilter('auction_id', $auctionIds);
        $result = array(
            'success' => array(),
            'error' => array()
        );
        foreach ($auctions as $auction) {
            try {
                $oldId = $auction->getId();
                $auction->allegroSellAgain($auction->getAuctionDuration(), $startingTimestamp);
                $auction->allegroVerify();
                $result['success'][] = $oldId;
            } catch(Orba_Allegro_Exception $e){
                $result['error'][] = array(
                    'id' => $auction->getId(),
                    'message' => $e->getMessage()
                );
            } catch (Exception $e){
                $result['error'][] = array(
                    'id' => $auction->getId(),
                    'message' => Mage::helper('orbaallegro')->__("Other error (check logs)")
                );
                Mage::logException($e);
            }
        }
        return $result;
    }
    
    protected function _checkNewAcution($data) {
        return $this->_getClient()->checkNewAuctionExt($data);
    }
    
    protected function _checkEditAuction($data) {
        return $this->_getClient()->checkChangeItemFields($data);
    }
    
    
    protected function _newAuction($data, $localId) {
        $data[Orba_Allegro_Model_Form_Parser_Out::KEY_LOCAL_ID] = $localId;
        return $this->_getClient()->newAuctionExt($data);
    }
    
    protected function _editAuction($data) {
        return $this->_getClient()->changeItemFields($data);
    }
    
    
    /**
     * Assign current  post-send data to auction
     * @param Orba_Allegro_Model_Auction $auction
     * @param array $auctionData
     */
    protected function _assingFormDataToAuction(Orba_Allegro_Model_Auction $auction, array $auctionData) {
        
            $service = $auction->getService();
            
            // Auction title
            if(isset($auctionData[$service::ID_TITLE])){
                $auction->setAuctionTitle($auctionData[$service::ID_TITLE]);
            }

            // Auction items qty
            if(isset($auctionData[$service::ID_QUANTITY])){
                $auction->setItemsPlaced($auctionData[$service::ID_QUANTITY]);
            }
            
            // Auction duration
            if(isset($auctionData[$service::ID_DURATION])){
                $durationId = $auctionData[$service::ID_DURATION];
                $mapModel = Mage::getModel("orbaallegro/system_config_source_attribute_template_duration");
                /* @var $mapModel Orba_Allegro_Model_System_Config_Source_Attribute_Template_Duration */
                $map = $mapModel->toOptionHash();
                if(isset($map[$durationId])){
                    $auction->setAuctionDuration($map[$durationId]);
                }
            }
			
			// Auction renew if fromat is shop
            if(isset($auctionData[$service::ID_AUTO_RENEW]) && isset($auctionData[$service::ID_SALES_FORMAT]) &&
					$auctionData[$service::ID_SALES_FORMAT]==Orba_Allegro_Model_System_Config_Source_Attribute_Template_Salesformat::FORMAT_SHOP){
                $auction->setRenewType($auctionData[$service::ID_AUTO_RENEW]);
            }else{
				 $auction->setRenewType(Orba_Allegro_Model_System_Config_Source_Attribute_Template_Autorenew::DO_NOT_RENEW);
			}
			
            
            // Auction price
            if(isset($auctionData[$service::ID_BUY_NOW_PRICE])){
                $auction->setAuctionItemPrice($auctionData[$service::ID_BUY_NOW_PRICE]);
            }
            
            // Auction additional
            if(isset($auctionData[$service::ID_ADDITIONAL_OPTIONS])){
                $additional = $auctionData[$service::ID_ADDITIONAL_OPTIONS];
                if(is_array($additional)){
                    $additional = array_sum($additional);
                }
                $auction->setAuctionAdditionalOptions($additional);
            }
            
            // Auction shop category based on extenral id 
            // It might be changed in last step
            if(isset($auctionData[$service::ID_SHOP_CATEGORY])){
                $shopCategory = $auctionData[$service::ID_SHOP_CATEGORY];
                if($shopCategory){
                    $shopCategoryModel = Mage::getModel("orbaallegro/shop_category")->load($shopCategory, "external_id");
                    if($shopCategoryModel->getId()){
                        $auction->setShopCategoryId($shopCategoryModel->getId());
                    }
                }
            }
    }
    
    
    /**
     * @return Orba_Allegro_Model_Client
     */
    protected function _getClient() {
        $store = Mage::registry("store");
        return Mage::getModel("orbaallegro/client")->
            addData($this->_getConfig()->getLoginData($store));
    }
    
    /**
     * @return Orba_Allegro_Model_Config
     */
    protected function _getConfig() {
        return  Mage::getModel("orbaallegro/config");
    }


    protected function _getCountryCode() {
        $store = Mage::registry('store');
        if(!($cc=$this->_getConfig()->getCountryCode($store))){
            throw new Orba_Allegro_Exception("No country code");
        }
        return $cc;
    }
    
    /**
     * @return Mage_Core_Model_Store
     */
    protected function _getStore() {
        return Mage::registry('store');
    }
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct() {
        return Mage::registry('product');
    }
    
    /**
     * @return Orba_Allegro_Model_Template
     */
    protected function _getTemplate() {
        return Mage::registry('template');
    }
    
    /**
     * @return Orba_Allegro_Model_Category
     */
    protected function _getCategory() {
        return Mage::registry('category');
    }
    
    /**
     * @return Orba_Allegro_Model_Shop_Category
     */
    protected function _getShopCategory() {
        return Mage::registry('shop_category');
    }
    
    /**
     * @return Orba_Allegro_Model_User
     */
    protected function _getUser() {
        return Mage::registry('user');
    }
    
    /**
     * @return Orba_Allegro_Model_Service_Abstract
     */
    protected function _getService() {
        return Mage::registry('service');
    }
    
    /**
     * @return Orba_Allegro_Model_Auction
     */
    protected function _getAuction() {
        return Mage::registry('auction');
    }

    /**
	 * Get Simple Product Id
	 * 
     * @return int Simple Product Id
     */
    protected function _getSimpleProductId() {
        return Mage::registry('product_id');
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
	 * Get Auction Buy Request: Super Attribute
	 * 
     * @return mixed string|boolean Buy Request Value
     */
    protected function _getBuyRequest() {
		$buyRequest = null;
		$superAttribute = $this->getRequest()->getParam('super_attribute');
		
		if ($superAttribute) {
			$buyRequest = serialize(array(
				'qty'				=> null,
				'super_attribute'	=> Mage::helper('core')->jsonDecode($superAttribute)
			));
		}
		
        return $buyRequest;
    }	

    protected function _registerObjects() {
        $request = $this->getRequest();
        if ($this->getRequest()->getPost('product')) {
            $postRequest = $this->getRequest()->getPost('product');
        }
		
        $models = array(
            'store'      => 'core/store', 
            'product'    => 'catalog/product', 
            'template'   => 'orbaallegro/template', 
            'category'   => 'orbaallegro/category',
            'shop_category'   => 'orbaallegro/shop_category'
        );
        
        $dataObject = Mage::getModel('orbaallegro/auction');
        if(($auctionId = $request->getParam('auction_id'))!==null){
            if(is_numeric($auctionId)){
                $dataObject->load((int)$auctionId);
            }
        }
        
        Mage::register('auction', $dataObject);
        
        $storeId = null;
        
        foreach ($models as $param=>$modelName) {
            $value = $dataObject->getId() ? $dataObject->getData($param."_"."id") : $request->getParam($param);
            $model = Mage::getModel($modelName);

            // Load store setting
            if($storeId && in_array($param, array("product", "template"))){
                $model->setStoreId($storeId);
            }
            
            // Init model
            if($model && !is_null($value)){
                $model->load($value);
            }
            if($param=="store"){
                if(!$model->getId() && Mage::app()->isSingleStoreMode()){
                    $model = Mage::app()->getStore(true);
                }
                $storeId = $model->getId();
            }
            
            Mage::register($param, $model);
        }
       
        if($storeId!==null){
            // Add user
            $user = Mage::getModel("orbaallegro/user", $storeId);
            Mage::register('user', $user);
            
            // Add service
            $serviceFactory = Mage::getSingleton('orbaallegro/service');
            Mage::register('service', $serviceFactory::factory($this->_getCountryCode()));
        }
		
		$parentProductId = $this->_getParentProductId();
		if (!$parentProductId) {
			$superAttributesJson = $request->getParam('super_attribute', false);
			if ($superAttributesJson) {
				$superAttributes = Mage::helper('core')->jsonDecode($superAttributesJson);
				$childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($superAttributes, $this->_getProduct());
				if ($childProduct && $childProduct->getId()) {
					$parentId = $this->_getProduct()->getId();
					Mage::register('parent_product_id', $parentId);
					Mage::register('product_id', $childProduct->getId());
				}
			}
		}
    }
	
	public function syncAction() {
        $model = Mage::getSingleton("orbaallegro/observer");
        /* @var $model Orba_Allegro_Model_Observer */
        
        try {
            $model->verifyAucitons();
            $model->checkPlacedAuctions();
			
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect("*/*/index");
        }
		
        $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__("Auctions Synchronized"));
		return $this->_redirect("*/*/index");
	}
}