<?php

class Orba_Allegro_Adminhtml_Auction_BidController extends Orba_Allegro_Controller_Adminhtml_Abstract {

    public function indexAction() {
        if (!is_null($msg = $this->_stopProcess())) {
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('Auctions'))
                ->_title($this->__('Bids'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction() {
        if (!is_null($msg = $this->_stopProcess())) {
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_registerModel();
        $this->_title($this->__('Sales'))
                ->_title($this->__('ORBA | Allegro'))
                ->_title($this->__('Auctions'))
                ->_title($this->__('View Bid'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createOrderAction() {

        if (!is_null($msg = $this->_stopProcess())) {
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }

        $bid = $this->_registerModel();
        $bidQty = $this->getRequest()->getParam('quantity');

        $this->_getQuoteSession()->clear();

        $create = Mage::getSingleton('orbaallegro/adminhtml_sales_order_create');
        /* @var $create Orba_Allegro_Model_Adminhtml_Sales_Order_Create */

        $auction = $bid->getAuction();
        $store = $auction->getStore();

        // Create quote for first item store
        $quote = $create->createFromBid($bid, $bidQty);

        // Copy telephone (in billign is mandatory)
        $create->copyAddressDataIfEmpty(
                Mage_Sales_Model_Order_Address::TYPE_BILLING, Mage_Sales_Model_Order_Address::TYPE_SHIPPING, array("telephone")
        );

        // Save Quote
        $quote->collectTotals();
		$quote->setIsActive(false);
        $quote->save();

        $this->_getQuoteSession()->setQuoteId($quote->getId());
        $this->_getQuoteSession()->setStoreId($store->getId());
        $this->_getQuoteSession()->setCurrencyId("PLN");

        $customer = $create->getQuote()->getCustomer();
        $this->_getQuoteSession()->setCustomerId($customer->getId() ? $customer->getId() : false);

        return $this->_redirect("*/sales_order_create");
    }

    public function refreshAction() {
        if (!is_null($msg = $this->_stopProcess())) {
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }

        $bid = $this->_registerModel();
        $newBidCount = 0;
        try {
            if (!$bid->getId()) {
                $model = Mage::getSingleton("orbaallegro/observer");
                /* @var $model Orba_Allegro_Model_Observer */
                $model->syncPlacedAuctionContractors();
                $model->syncPlacedAuctionBids($newBidCount);
                $message = $newBidCount > 0 ?
                        "There are %s new bid(s)" : "There are no new bids";
            } else {
                $auction = $bid->getAuction();
                $auction->allegroSyncAuctionContractors();
                $auction->allegroSyncAuctionBids();
                $message = "Bid synced";
            }
        } catch (Orba_Allegro_Exception $e) {
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Other error (check logs)"));
            Mage::logException($e);
            return $this->_redirectReferer();
        }

        $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__($message, $newBidCount));
        return $this->_redirectReferer();
    }

    public function ignoreAction() {
        if (!is_null($msg = $this->_stopProcess())) {
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = $this->_registerModel();

        if (!$model->getId()) {
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Bid dont exists"));
            return $this->_redirect("*/*");
        }
        $mode = (int) $this->getRequest()->getParam('mode', 0);

        $model->setIsIgnored($mode);
        $model->save();
        $this->_getSession()->addSuccess(
                Mage::helper('orbaallegro')->__($mode ? "Bid ignored" : "Bid unignored")
        );
        return $this->_redirectReferer();
    }

    public function addNoteAction() {
        if (!is_null($msg = $this->_stopProcess())) {
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        
        $bid = $this->_registerModel();
        $request = $this->getRequest();
        
        $user = $user = Mage::getSingleton('admin/session')->getUser();
        /* @var $user Mage_Admin_Model_User */
        
        $content = $request->getParam("content", "");
        
        if(!$bid->getId()){
            $this->_getSession()->addError(
                Mage::helper('orbaallegro')->__("Bid not exists.")
            );
            return $this->_redirectReferer();
        }
        
        if(!$user->getId()){
            $this->_getSession()->addError(
                Mage::helper('orbaallegro')->__("Admin user not exists.")
            );
            return $this->_redirectReferer();
        }
        
        if(empty($content)){
            $this->_getSession()->addError(
                Mage::helper('orbaallegro')->__("Note is empty.")
            );
            return $this->_redirectReferer();
        }
        
        $data = array(
            "content" => $content,
            "bid_id" => $bid->getId(),
            "user_id" => $user->getId(),
            "user_name" => $user->getUsername(),
        );
        
        try{
            $model = Mage::getModel("orbaallegro/auction_bid_note");
            $model->setData($data);
            $model->save();
        }catch(Exception $e){
            $this->_getSession()->addError(
                Mage::helper('orbaallegro')->__("Some error (check logs)")
            );
            Mage::logException($e);
            return $this->_redirectReferer();
        }
        
        $this->_getSession()->addSuccess(
            Mage::helper('orbaallegro')->__("Note saved")
        );
        return $this->_redirectReferer();
    }
    
    /**
     * @return Orba_Allegro_Model_Auction_Bid
     */
    public function _registerModel() {
        if (!Mage::registry('orbaallegro_current_auction_bid')) {
            $model = Mage::getModel('orbaallegro/auction_bid');
            if ($id = $this->getRequest()->getParam('bid_id')) {
                $model->load($id);
            }
            Mage::register('orbaallegro_current_auction_bid', $model);
        };
        return Mage::registry('orbaallegro_current_auction_bid');
    }

    /**
     * Retrieve session object
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getQuoteSession() {
        return Mage::getSingleton('adminhtml/session_quote');
    }
	
	public function unhandledItemCountAction() {
        $backUrl = $this->_getRefererUrl();
        $postData = $this->getRequest();
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            $this->getResponse()->setRedirect($backUrl);
        }
		
        if ($postData && $postData->getParam('bid_id')) {
			$bid = $this->_registerModel();
            if ($bid) {
                $unhandledItemCount = $bid->getUnhandledItemCount(true);
            } else {
				$unhandledItemCount = false;
			}
            
            if (is_bool($unhandledItemCount)) {
                //Return an empty JSON Formated String for Ajax Request
                $resultJSON = '{}';
            } else {
                $result['status'] = true;
                $result['message'] = $unhandledItemCount;
                $resultJSON = Mage::helper('core')->jsonEncode($result);
            }
            $this->getResponse()->setBody($resultJSON);
        } else {
            //Redirect to Referer Url if neccessary data nor present within Controller Call
            $this->getResponse()->setRedirect($backUrl);
        }
	}
}