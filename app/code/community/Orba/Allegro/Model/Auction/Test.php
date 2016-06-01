<?php

class Orba_Allegro_Model_Auction_Test extends Mage_Core_Model_Abstract
{
    public function test($auctionData)
    {
        $service = Orba_Allegro_Model_Service::factory($this->_getCountryCode());

        if($service->getId()){
            $parser = Mage::getModel("orbaallegro/form_parser_out", $service);
            /* @var $parser Orba_Allegro_Model_Form_Parser_Out */
            $parsedData = $parser->parse($auctionData);

            $error = false;
            $result = "";

            try{
                $result = $this->_checkNewAuction($parsedData);
            }catch(Exception $e){
                $error = $e->getMessage();
            }

            $t = 1;
        }
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
     * @return Orba_Allegro_Model_Auction
     */
    protected function _getAuction() {
        return Mage::registry('auction');
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
     * Sends prepared data to Allegro service
     *
     * @param $data
     * @return type
     */
    protected function _checkNewAuction($data) {
        return $this->_getClient()->checkNewAuctionExt($data);
    }
}