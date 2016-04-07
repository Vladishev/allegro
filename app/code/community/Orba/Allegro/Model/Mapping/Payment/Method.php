<?php

class Orba_Allegro_Model_Mapping_Payment_Method extends Mage_Payment_Model_Method_Abstract {

    const PAYMENT_METHOD_BANKTRANSFER_CODE = 'orbaallegro';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_BANKTRANSFER_CODE;
    
    /**
     * Only admin
     * @var bool
     */
    protected $_canUseCheckout = false;
    
    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    
    /**
     * @var Orba_Allegro_Model_Transaction
     */
    protected $_transaction;


    public function isAvailable($quote = null) {
        $this->setQuote($quote);
        return parent::isAvailable($quote);
    }
    
    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }
    
    public function canUseInternal() {
        if(!$this->getAllegroPayment()){
            return false;
        }
        // Todo: check condition in quote
        return parent::canUseInternal();
    }
    
    public function getTitle() {
        $extendedInfo = parent::getTitle();
        if($this->getAllegroPayment()){
            $extendedInfo .= " (" . Mage::helper("orbaallegro")->__(
                $this->getAllegroPayment()->getName()) . ")";
        }
        return $extendedInfo;
    }
    
    
    /**
     * @return Mage_Sales_Model_Quote|null
     */
    public function getQuote() {
        return $this->_quote;
    }
    
    
    /**
     * @return Mage_Sales_Model_Quote
     */
    public function setQuote($quote) {
        $this->_quote = $quote;
        return $this;
    }
    
    
    public function getAllegroPayment() {
        if($this->getTransaction()){
            return $this->getTransaction()->getPayment();
        }
        return false;
    }
    
    /**
     * @return Orba_Allegro_Model_Transaction|false
     */
    public function getTransaction() {
        if($this->_transaction===null){
            $this->_transaction = false;
            
            $transactionId = null;
            
            if($this->getQuote()){
                $transactionId= $this->getQuote()->getOrbaallegroTransactionId();
            }else{
                $info = $this->getInfoInstance();
                if(is_object($info) && $info->getId() && $info->getOrder()){
                    $order = $info->getOrder();
                    $transactionId=$order->getOrbaallegroTransactionId();
                }
            }
            
            if($transactionId!==null){
                $model = Mage::getModel("orbaallegro/transaction")->load($transactionId);
                if($model->getId()){
                    $this->_transaction = $model;
                }
            }
        }
        return $this->_transaction;
    }
}