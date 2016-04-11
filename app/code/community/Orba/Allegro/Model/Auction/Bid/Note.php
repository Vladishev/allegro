<?php
class Orba_Allegro_Model_Auction_Bid_Note extends Mage_Core_Model_Abstract{
    

    protected function _construct() {
        $this->_init('orbaallegro/auction_bid_note');
    }
    
    /**
     * @return Mage_Admin_Model_User
     */
    public function getUser() {
        if(!$this->getData("user")){
            $user = Mage::getModel("admin/user");
            $user->load($this->getUserId());
            $this->setData("user", $user);
        }
        return $this->getData("user");
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Note
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Note_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
}