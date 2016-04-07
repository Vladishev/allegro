<?php
/**
 * Tab with flatform using for edit mode
 */
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Flat
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {   
        $auction = $this->getAuction();
        /* @var $auction Orba_Allegro_Model_Auction */
        
        $store = $this->getStore();
        /* @var $store Mage_Core_Model_Store */
        
        $user = $this->getUser();
        /* @var $store Orba_Allegro_Model_User */
        
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */
        
        $template = Mage::registry('template');
        /* @var $template Mage_Catalog_Model_Template */
        
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        
        $config = Mage::getSingleton("orbaallegro/auction_config");
        /* @var $config Orba_Allegro_Model_Auction_Config */
        $countryCode = $config->getCountryCode($store);
                
        // Init client
        $client->setData($config->getLoginData($store));
        
        $form = Mage::getModel("orbaallegro/form_auction", 
                array('country_code'=>$countryCode, 'edit_mode'=>true));
        /* @var $form Orba_Allegro_Model_Form_Auction */
        
        
        
        $form->setClient($client);
        $form->setCountryCode($countryCode);
        $form->load($this->getCategory()->getExternalId());
        

        $form->prepareAuctionValues($auction);
        
        
        // Fill category
        if($catField = $form->getField($form::FIELD_CATEGORY)){
            $catField->setShowExternalValue(true);
            $catField->setCountryCode($countryCode);
            $catField->setValue($this->getCategory()->getId());
        }
        
        // Fill shop category
        if($shopCatField = $form->getField($form::FIELD_SHOP_CATEGORY)){
            $shopCatField->setShowExternalValue(true);
            $shopCatField->setCountryCode($countryCode);
            $shopCatField->setIsAuctionForm(true);
            $id = $this->getShopCategory()->getId();
            $shopCatField->setValue(($id == null) ? 0 : $id);
        }
        

        
        $this->setForm($form);
        Mage::dispatchEvent("orbaallegro_auction_form_prepared", array("form"=>$form));
                
    }
    
	public function getFormHtml() {
		return Mage::helper('orbaallegro')->rebuildAuctionFormHtml(parent::getFormHtml());
	}

    
}
