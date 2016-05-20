<?php

class Tim_UpdateQuantity_Model_Webservice extends Mage_Core_Model_Abstract
{
    /*
     * soap client
     */
    protected $soap;
    
    /* 
     * soap session
     */
    protected $session;


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
    
    protected function getSoap(){
        if(!$this->soap){
            // temporary
    //        $url = 'http://webapi.allegro.pl/uploader.php?wsdl';
            $useSandbox = Mage::getStoreConfig(
                Orba_Allegro_Model_Config::XML_PATH_CONFIG_SANDBOX_SELECT,
                Mage::app()->getStore()
            );
            if ($useSandbox == 1) {
                $url = Mage::getStoreConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_SANDBOX_API_URL);
            } else {
                $url = Mage::getModel('orbaallegro/config')->getApiUrl();
            }
            $webapi_key = 's7a9f3a8';
            $user_id = 'outletelektryka';
            $user_pass = '7a9f3a8bd64c1a5d';
            $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
            try {
                $this->soap = new SoapClient($url, $options);
                $request = array(
                    'countryId' => 1,
                    'webapiKey' => $webapi_key
                );
                $result = $this->soap->doQueryAllSysStatus($request);

                $versionKeys = array();
                foreach ($result->sysCountryStatus->item as $row) {
                    $versionKeys[$row->countryId] = $row;
                }

                $request = array(
                    'userLogin' => $user_id,
                    'userHashPassword' => base64_encode( hash('sha256',$user_pass,true)),
                    'countryCode' => 1,
                    'webapiKey' => $webapi_key,
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
    
    protected function getSoapSession(){
        if(!$this->session){
            $this->getSoap();
        }
        return $this->session;
    }

    /**
     * Get soap session
     * @return mixed
     */
    public function getServiceSession()
    {
        return $this->getSoapSession();
    }

    /**
     * Send request to webservice
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

    public function doGetSiteJournalDeals(){
        $transtactionIds = array();
        do{
            $request = array();
            if($lastElement->dealEventId){
                $request['journalStart'] = $lastElement->dealEventId;
            }

            $result = $this->_runWebservice('doGetSiteJournalDeals', $request,'sessionId')->siteJournalDeals->item;
            foreach($result as $item){
                if($item->dealEventType === 2){ //transaction type id
                    $transtactionIds[$item->dealTransactionId] = $item->dealTransactionId;
                }
            }
            foreach($result as $item){
                if($item->dealEventType === 3){ //transaction type id
                    unset($transtactionIds[$item->dealTransactionId]);
                }
            }            
            
            $lastElement = end($result);
        } while(count($result) == 100);

        return array_unique($transtactionIds);
    }
    
//    public function getAuctions(){
//        $request = array(
//            'pageSize' => 1000
//        );
//        
//        return $this->_runWebservice('doGetMySoldItems',$request,'sessionId');
//    }
//    
//    public function getTransactionIds(array $auctionIds){
//        $length = 25;
//        $offset = 0;
//        
//        $transtactionIds = array();
//        for( $i=0 ; $i<ceil(count($auctionIds) / $length); $i++ ){
//            $request = array(
//                'itemsIdArray' => array_slice($auctionIds, ($offset + $length)*$i, $length),
//                'userRole' => 'seller'
//            );
//            
//            $transtactionIdsPackage = $this->_runWebservice('doGetTransactionsIDs',$request)->transactionsIdsArray->item;
//            $transtactionIds = array_merge($transtactionIds,$transtactionIdsPackage);
//
//        }
                


//        return $transtactionIds;
//    }

    
    public function getOrders(array $transactionIds){
        $length = 25;
        $offset = 0;
        
        $orders = array();
        for( $i=0 ; $i<ceil(count($transactionIds) / $length); $i++ ){
            $request = array(
                'transactionsIdsArray' => array_slice($transactionIds, ($offset + $length)*$i, $length)
            );
            
            $ordersPackage = $this->_runWebservice('doGetPostBuyFormsDataForSellers',$request,'sessionId')->postBuyFormData->item;
            $orders = array_merge($orders,$ordersPackage);

        }
        
        return $orders;
    }

//    public function getCustomerDetails(array $allegroCustomersIds){
//        $request = array(
//            'auctionIdList' => array(5073212539,5140591246,5138170950),
//            'offset'        => 0,
//            'desc'          => 0
//            
//        );
//
//        $tmp = $this->_runWebservice('doMyContact',$request);
//        echo '<pre>';
//        var_dump($tmp);
//        die('ala ma kota');
//        die;
//        
//        
//        
//        
//        $customerDetails = $this->_runWebservice('doGetPostBuyData',$request)->transactionsIdsArray->item;
//    }
    
    
    
    
    
    
        public function soapConnectionTmp(){
        // temporary
//        $url = 'http://webapi.allegro.pl/uploader.php?wsdl';
        $url = 'https://webapi.allegro.pl/service.php?wsdl';
        $webapi_key = '4cdc2023';
        $user_id = 'outletelektryka';
        $user_pass = 'timsa556';


//        $client = new SoapClient($url);
//        $version = $client->doQuerySysStatus(1,1,$webapi_key);
//        $login = $client->doLoginEnc($user_id,base64_encode( hash('sha256',$user_pass,true)),1,$webapi_key, $version['ver-key']);
        
        
        
        echo '<pre>';

        $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        try {
            $soapClient = new SoapClient('https://webapi.allegro.pl/service.php?wsdl', $options);
            $request = array(
                'countryId' => 1,
                'webapiKey' => $webapi_key
            );
            $result = $soapClient->doQueryAllSysStatus($request);
            
            $versionKeys = array();
            foreach ($result->sysCountryStatus->item as $row) {
                $versionKeys[$row->countryId] = $row;
            }
            
            $request = array(
                'userLogin' => $user_id,
                'userHashPassword' => base64_encode( hash('sha256',$user_pass,true)),
                'countryCode' => 1,
                'webapiKey' => $webapi_key,
                'localVersion' => $versionKeys[1]->verKey,
            );
            $session = $soapClient->doLoginEnc($request);
            //-------------------
            //lista aukcji
            $request = array(
                'sessionId' => $session->sessionHandlePart,
                'pageSize' => 10
            );
 
//            $myWonItems = $soapClient->doGetMySoldItems($request);
//            var_dump($myWonItems);
//            die;
            //-------------------
            $auction_number = array(4881568613,4900496726,4914073541);
            
            //kupujący - adres wysyłki
            $domycontact_request = array(
                'sessionHandle' => $session->sessionHandlePart,
                'itemsArray' => $auction_number
             );
            
//            $contacts = $soapClient->doGetPostBuyData($domycontact_request);
            
            
            //pobranie transactions id dla danych aukcji
            
            $dogettransactionsids_request = array(
                'sessionHandle' => $session->sessionHandlePart,
                'itemsIdArray' => $auction_number,
                'userRole' => 'seller',
//                'shipmentIdArray' => array(2, 5)
             );
            
//            $transactionsIDs = $soapClient->doGetTransactionsIDs($dogettransactionsids_request);
//            var_dump($transactionsIDs);
//            die('ok');
            
            //-------------------
            // pobranie wszystkich parametrów sprzedaży
            $transactionsIDs = array(409843587);
            $dogetpostbuyformsdataforsellers_request = array(
                'sessionId' => $session->sessionHandlePart,
                'transactionsIdsArray' => $transactionsIDs
            );
            
            $customers = $soapClient->doGetPostBuyFormsDataForSellers($dogetpostbuyformsdataforsellers_request);
            
            
            /**
             * dane zwracane:
             * id aukcji
             * dane do faktury
             * dane do wysyłki
             * 
             */
            
            
            
            var_dump($customers);
            die('ok');
            
            
        } catch(Exception $e) {
            echo $e;
            die('error');
        }
        
    }
}


