<?php

class Orba_Allegro_Adminhtml_TransactionController extends Orba_Allegro_Controller_Adminhtml_Abstract {

    protected $_hasError = false;
    
    public function indexAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Sales'))
                ->_title($this->__('Allegro.pl'))
                ->_title($this->__('Transaction'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function auctionGridAction() {
        $this->_registerModel();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function viewAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = $this->_registerModel();
        
        if(!$model->getId()){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Transaction dont exists"));
            return $this->_redirect("*/*");
        }
        
        $this->_title($this->__('Sales'))
                ->_title($this->__('Allegro.pl'))
                ->_title($this->__('View transaction'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function ignoreAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = $this->_registerModel();
        
        if(!$model->getId()){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Transaction dont exists"));
            return $this->_redirect("*/*");
        }
        $mode = (int)$this->getRequest()->getParam('mode', 0);
        
        $model->setIsIgnored($mode);
        $model->save();
        $this->_getSession()->addSuccess(
                Mage::helper('orbaallegro')->__($mode ? "Auction ignored" : "Transaction unignored")
        );
        return $this->_redirectReferer();
    }
    
    
    public function refreshAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        
        $transaction = $this->_registerModel();
        $newTransactionCount = 0;
        
        try{
            if(!$transaction->getId()){
                $model = Mage::getSingleton("orbaallegro/observer");
                /* @var $model Orba_Allegro_Model_Observer */
                $model->syncPlacedAuctionContractors();
                $model->syncPlacedAuctionBids();
                $model->checkPlacedAuctionTransactions($newTransactionCount);
                $message = $newTransactionCount>0 ? 
                    "There are %s new transaction(s)" : "There are no new transactions";
            }else{
                foreach($transaction->getAuctionCollection() as $auction){
                    /* @var $auction Orba_Allegro_Model_Auction*/
                    // Get new contractors
                    $auction->allegroSyncAuctionContractors();
                    $auction->allegroSyncAuctionBids();
                }
                $transaction->allegroSync();
                $message = "Transaction synced";
            }
        }catch(Orba_Allegro_Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Allegro Error") . ": " . $e->getMessage());
            return $this->_redirectReferer();
        }catch(Exception $e){
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__("Other error (check logs)"));
            Mage::logException($e);
            return $this->_redirectReferer();
        }
        
        $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__($message, $newTransactionCount));
        return $this->_redirectReferer();
    }
    
    public function placeOrderAction() {
        die("AUTOPLACE");
    }
    
    public function createOrderAction() {
        
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        
        $transaction = $this->_registerModel();
        
        $this->_getQuoteSession()->clear();
        
        

        $create = Mage::getSingleton('orbaallegro/adminhtml_sales_order_create');
        /* @var $create Orba_Allegro_Model_Adminhtml_Sales_Order_Create */
        

        $auctions = $transaction->getAuctionCollection();
        $store = $auctions->getFirstItem()->getStore();
        
        // Create quote for first item store
        $quote = $create->createQuoteFromTransaction($transaction, $store);
        
        // Add dome data from allegro account
        $shipping = $quote->getShippingAddress();
        $billing = $quote->getBillingAddress();
        $contractor = $transaction->getContractor();
        
        if($contractor && $contractor->getId() && $contractor->getPhone()){
            // Phone
            if(!$shipping->getTelephone()){
                $shipping->setTelephone($contractor->getPhone());
            }
            if(!$billing->getTelephone()){
                $billing->setTelephone($contractor->getPhone());
            }
        }
        
        // Copy same required values
        $create->copyAddressDataIfEmpty(
            Mage_Sales_Model_Order_Address::TYPE_SHIPPING,
            Mage_Sales_Model_Order_Address::TYPE_BILLING,
            array("telephone", "firstname", "lastname")
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

    /**
     * @return Orba_Allegro_Model_Transaction
     */
    public function _registerModel() {
        if(!Mage::registry('orbaallegro_current_transaction')){
            $model = Mage::getModel('orbaallegro/transaction');
            if ($id = $this->getRequest()->getParam('transaction')) {
                $model->load($id);
            }
            Mage::register('orbaallegro_current_transaction', $model);
        };
        return Mage::registry('orbaallegro_current_transaction');
    }
    
    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getQuoteSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

}