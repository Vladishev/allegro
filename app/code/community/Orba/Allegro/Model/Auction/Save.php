<?php

/**
 * Class Orba_Allegro_Model_Auction_Save
 *
 * Implements 'save auction' for mass action
 */
class Orba_Allegro_Model_Auction_Save extends Mage_Core_Model_Abstract
{
    /**
     * @see Orba_Allegro_Block_Adminhtml_Auction_Edit_Tabs
     * @param array $auctionData
     * @throws Orba_Allegro_Exception
     * @return void
     */
    public function save($auctionData)
    {
        if(!$this->_getStore()->getId() || !$this->_getProduct()->getId() || !$this->_getCategory()){
            //Catch error here
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/catalog_product'));

        }

        if(!is_null($msg = $this->_stopProcess(true, $this->_getStore()->getId()))){
            Mage::getSingleton('core/session')->addError($msg);
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/catalog_product'));
        }
        // Prepare data

        $service = Orba_Allegro_Model_Service::factory($this->_getCountryCode());

        if(!$service->getId()){
            Mage::getSingleton('core/session')->addError('No service');
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/catalog_product'));
        }

        try{
            $parserOut = Mage::getModel("orbaallegro/form_parser_out", $service);
            /* @var $parserOut Orba_Allegro_Model_Form_Parser_Out */

            $auction = Mage::getModel("orbaallegro/auction");
            /* @var $auction Orba_Allegro_Model_Auction */
            $config = $this->_getConfig();

            $parsedData = $parserOut->parse($auctionData);

            $productId = $this->_getProduct()->getId();

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
                "buy_request"			=> null /*$this->_getBuyRequest()*/
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

        }catch(Orba_Allegro_Model_Client_Exception $e){
            Mage::log($e->getMessage());
        }catch(Exception $e){
            Mage::log($e->getMessage());
//            Mage::logException($e);
        }
    }

    /**
     * Stops action if user not logged in for Orba Allegro service
     *
     * @param bool $checkLogin
     * @param null $storeId
     * @return null|string
     */
    protected function _stopProcess($checkLogin = false, $storeId = null)
    {
        $helper = Mage::helper('orbaallegro');
        /* @var $helper Orba_Allegro_Helper_Data */

        if (!$helper->isFirstImportComplete()) {
            return Mage::helper('orbaallegro')->__(
                "Configuration is incomplte. " .
                "Login to Allegro service "
            );
        }

        $config = Mage::getModel("orbaallegro/config");
        /* @var $config Orba_Allegro_Model_Config */

        if ($checkLogin && is_null($storeId)) {
            Mage::helper('orbaallegro')->__("No store specified");
        }

        if ($checkLogin && !$helper->canLogin($storeId)) {
            $config = Mage::getModel("orbaallegro/config");
            /* @var $config Orba_Allegro_Model_Config */

            return Mage::helper('orbaallegro')->__(
                "Wrong login data (depend on store view). " .
                "Login to Allegro service."
            );
        }

        return null;
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

    /**
     * Assign passed product data to auction
     *
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
     * Save auction to Allegro with local Id
     *
     * @param array $data
     * @param string $localId
     * @return object stdClass
     */
    protected function _newAuction($data, $localId) {
        $data[Orba_Allegro_Model_Form_Parser_Out::KEY_LOCAL_ID] = $localId;
        return $this->_getClient()->newAuctionExt($data);
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
     * @return mixed
     * @throws Orba_Allegro_Exception
     */
    protected function _getCountryCode() {
        $store = Mage::registry('store');
        if(!($cc = $this->_getConfig()->getCountryCode($store))){
            throw new Orba_Allegro_Exception("No country code");
        }

        return $cc;
    }

    /**
     * @return Orba_Allegro_Model_Config
     */
    protected function _getConfig() {
        return  Mage::getModel("orbaallegro/config");
    }

    /**
     * @return Orba_Allegro_Model_Template
     */
    protected function _getTemplate() {
        return Mage::registry('template');
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
     * @return Orba_Allegro_Model_Category
     */
    protected function _getCategory() {
        return Mage::registry('category');
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
     * Returns parent product id if exist
     *
     * @return mixed
     */
    protected function _getParentProductId() {
        return Mage::registry('parent_product_id');
    }
}