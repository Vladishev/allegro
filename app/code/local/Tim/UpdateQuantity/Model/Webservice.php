<?php

/**
 * Class Tim_UpdateQuantity_Model_Webservice
 *
 * Works with allegro via soap
 */
class Tim_UpdateQuantity_Model_Webservice extends Mage_Core_Model_Abstract
{
    /**
     * @var object Soap client
     */
    protected $soap;

    /**
     * @var object Soap session
     */
    protected $session;

    /**
     * Returns external function from wsdl if it exist
     *
     * @param string $func
     * @param array $request
     * @param string $sessionLabel
     * @return mixed
     */
    protected function _runWebservice($func,array $request,$sessionLabel = 'sessionHandle'){
        try{
            $request[$sessionLabel] = $this->getSoapSession()->sessionHandlePart;
            return $this->getSoap()->{$func}($request);
        } catch (Exception $ex) {
            // send mail here
            var_dump($ex->getMessage()); 
            die;
        }
    }

    /**
     * Returns soap client
     *
     * @return object|SoapClient
     */
    protected function getSoap(){
        if(!$this->soap){
            $useSandbox = Mage::getStoreConfig(
                Orba_Allegro_Model_Config::XML_PATH_CONFIG_SANDBOX_SELECT,
                Mage::app()->getStore()
            );
            if ($useSandbox == 1) {
                $url = Mage::getStoreConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_SANDBOX_API_URL);
            } else {
                $url = Mage::getModel('orbaallegro/config')->getApiUrl();
            }
            $configData = Mage::getStoreConfig('orbaallegro/config');

            $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
            try {
                $this->soap = new SoapClient($url, $options);
                $request = array(
                    'countryId' => 1,
                    'webapiKey' => $configData['api_key']
                );
                $result = $this->soap->doQueryAllSysStatus($request);

                $versionKeys = array();
                foreach ($result->sysCountryStatus->item as $row) {
                    $versionKeys[$row->countryId] = $row;
                }

                $request = array(
                    'userLogin' => $configData['user_login'],
                    'userHashPassword' => base64_encode( hash('sha256',$configData['user_password'],true)),
                    'countryCode' => 1,
                    'webapiKey' => $configData['api_key'],
                    'localVersion' => $versionKeys[1]->verKey,
                );
                $this->session = $this->soap->doLoginEnc($request);
                
            } catch(Exception $e) {
                echo $e;
                die('error');
            }
        }

        return $this->soap;
    }

    /**
     * @return object Soap
     */
    protected function getSoapSession(){
        if(!$this->session){
            $this->getSoap();
        }
        return $this->session;
    }

    /**
     * Get soap session
     *
     * @return mixed
     */
    public function getServiceSession()
    {
        return $this->getSoapSession();
    }

    /**
     * Send request to webservice
     *
     * @param $request array
     * @return bool
     */
    public function doFinishItems($request)
    {
        if (is_array($request)) {
            $result = array($this->_runWebservice('doFinishItems', $request));
            if($result){
                return true;
            }else{
                Mage::log('There was a problem with the server', NULL, 'doFinishItems.log');
                return false;
            }
        }else{
            Mage::log('Auction_number ids are empty', NULL, 'doFinishItems.log');
            return false;
        }
    }
}


