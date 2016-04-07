<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Store
    extends Mage_Adminhtml_Block_Store_Switcher
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('orbaallegro/auction/edit/tab/store.phtml');
        $this->setId('orbaallegro_auction_store');
    }
    
    public function getWebsiteIds() {
        $product = $this->getProduct();
        /* @var $product Mage_Catalog_Model_Product */
        if($product && $product->getId()){    
            return $product->getWebsiteIds();
        }
        return parent::getWebsiteIds();
    }
    
    public function getServiceByStore(Mage_Core_Model_Store $store) {
        $config = Mage::getSingleton('orbaallegro/config');
        /* @var $config Orba_Allegro_Model_Config */
        $countryId = $config->getCountryCode($store);
        
        return Mage::getModel('orbaallegro/service')->load($countryId, "service_country_code");
    }
    
    public function getLoginByStore(Mage_Core_Model_Store $store) {
        $config = Mage::getSingleton('orbaallegro/config');
        /* @var $config Orba_Allegro_Model_Config */
        return $config->getLogin($store);
    }
}
