<?php
class Orba_Allegro_Model_Auction_Status{
	/**
	 * Save only local
	 */
    const STATUS_LOCALY                 = "localy";
	/**
	 * Saved in allegro but not verfie
	 */
    const STATUS_NO_VERIFIED            = "no_verified";
	/**
	 * Ignored (-1)
	 */
    const STATUS_IGNORED                = "ignored";
	/**
	 * Waiting (0)
	 */
    const STATUS_WAITING                = "waitnig";
	/**
	 * Placed (1)
	 */
    const STATUS_PLACED                 = "placed";
	/**
	 * Incoming (2)
	 */
    const STATUS_INCOMING               = "incoming";
	/**
	 * Sell agian (3)
	 */
    const STATUS_SELL_AGIAN             = "sell_again";
	/**
	 * Canceled by system
	 */
    const STATUS_CANCELED               = "canceled"; 
	/**
	 * Ended by user
	 */
    const STATUS_ENDED                  = "ended";
	/**
	 * Finished by system (natural)
	 */
    const STATUS_FINISHED               = "finished"; 
    
    protected $_statusByVerfiyCode = array(
        -1 => self::STATUS_IGNORED,
         0 => self::STATUS_WAITING,
         1 => self::STATUS_PLACED,
         2 => self::STATUS_INCOMING,
         3 => self::STATUS_SELL_AGIAN,
    );
    
    protected $_statusByEndingInfo = array(
         1 => self::STATUS_PLACED,
         2 => self::STATUS_FINISHED,
         3 => self::STATUS_ENDED,
    );
    
    public function toOptionHash() {
        $helper = Mage::helper("orbaallegro");
        return array(
            self::STATUS_LOCALY         => $helper->__("Saved localy"),
            self::STATUS_NO_VERIFIED    => $helper->__("Not verified"),
            self::STATUS_IGNORED        => $helper->__("Abused"),
            self::STATUS_WAITING        => $helper->__("Waiting"),
            self::STATUS_PLACED         => $helper->__("Placed"),
            self::STATUS_INCOMING       => $helper->__("Incoming"),
            self::STATUS_SELL_AGIAN     => $helper->__("Selling again"),
            self::STATUS_CANCELED       => $helper->__("Canceled"),
            self::STATUS_ENDED          => $helper->__("User ended"),
            self::STATUS_FINISHED       => $helper->__("Finished"),
        );
    }
    
    public function getStatusByCode($code) {
        if(isset($this->_statusByVerfiyCode[$code])){
            return $this->_statusByVerfiyCode[$code];
        }
        return null;
    }
    
    public function getStatusByEndingInfo($code) {
        if(isset($this->_statusByEndingInfo[$code])){
            return $this->_statusByEndingInfo[$code];
        }
        return null;
    }
    
}