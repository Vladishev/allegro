<?php
class Orba_Allegro_Block_Adminhtml_Auction_Grid_Column_Renderer_Link 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
        
    protected $_services = array();
    
    public function render(Varien_Object $row)
    {
        if(is_null($row->getAllegroAuctionId())){
            return "";
        }
        
        if(!in_array($row->getAuctionStatus(), $this->_getOnlineStatuses())){
            return "";
        }
        
        $service = $this->getService($row->getCountryCode());
        return '<a href="'.$service->getAuctionLink($row->getAllegroAuctionId()).'" target="_blank">'.
               Mage::helper("orbaallegro")->__("View online").
               '</a>';
    }
    
    public function getService($countryCode) {
        if(!isset($this->_services[$countryCode])){
            $this->_services[$countryCode] = Orba_Allegro_Model_Service::factory($countryCode);
        }
        return  $this->_services[$countryCode];
    }
    
    public function _getOnlineStatuses() {
        if(!$this->getData('online_statuses')){
            $this->setData('online_statuses', array(
                Orba_Allegro_Model_Auction_Status::STATUS_PLACED
            ));
        }
        return $this->getData('online_statuses');
    }
}
