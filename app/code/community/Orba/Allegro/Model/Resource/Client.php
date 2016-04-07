<?php
/**
 * @todo Clear and improve data transfer logs
 */
class Orba_Allegro_Model_Resource_Client extends SoapClient {
    
    const ERR_INVALID_VERSION_CAT_SELL_FIELDS = 'ERR_INVALID_VERSION_CAT_SELL_FIELDS';
    
    /**
     * @var SoapClient 
     */
    protected $_client;
    
    protected $_debug = false;
    
    public function __construct() {
        $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        if(Mage::helper("orbaallegro")->getIsDebugMode()){
            $options['trace'] = true;
            $this->_debug = true;
        }
        parent::__construct($this->getConfig()->getApiUrl(), $options);
    }
    
    /**
     * Executes Allegro API method. Returns false on error.
     * 
     * @param string $method
     * @param array $request
     * @param Orba_Allegro_Model_Client_Cache_Option_Interface $cacheopts model
     * @return boolean|stdClass
     */
    public function execute($method, $request, Orba_Allegro_Model_Client $clientObejct) {
        
        $result = false;
        $repeat = false;
        try {
            $result = $this->{$method}($request);
        } catch (Exception $e) {
            if($this->_debug){
                Mage::log(
                    "\n" . 'ERROR CODE: ' . $e->faultcode
                    . "\n" . 'ERROR MESSAGE: ' . $e->getMessage() 
                    . "\n" . 'METHOD: ' . $method 
                    . "\n" . 'REQUEST: '
                    . "\n " . var_export($request, true)
                    . "\n" . '--------------------------------------------', null, 'allegro.log');
            }
            if ($e->faultcode == self::ERR_INVALID_VERSION_CAT_SELL_FIELDS) {
                // Local version key is outdated. We'll get the new one.
                $request['localVersion'] = $clinetObject->getVerKey();
                $repeat = true;
            }else{
                throw new Orba_Allegro_Model_Client_Exception($e->faultcode . ": ". $e->getMessage());
            }
        }
        if ($repeat) {
            try {
                $result = $this->{$method}($request);
            } catch (Exception $e) {
                if($this->_debug){
                    Mage::log(
                        "\n Second call"
                        . "\n" . 'ERROR CODE: ' . $e->faultcode
                        . "\n" . 'ERROR MESSAGE: ' . $e->getMessage() 
                        . "\n" . 'REQUEST: '
                        . "\n " . var_export($request, true)
                        . "\n" . '--------------------------------------------', null, 'allegro.log');
                }
                throw new Orba_Allegro_Model_Client_Exception($e->faultcode . ": ". $e->getMessage());
            }
        }
        
        // 1000 / 50 = 20
        // Do not do DOS 
        usleep(20);
        
        return $result;
    }
   
    
    /**
     * Gets extension config model singleton.
     * 
     * @return Orba_Allegro_Model_Config
     */
    protected function getConfig() {
        return Mage::getSingleton('orbaallegro/config');
    }
    
   
    
    protected function _dumpData($request, $response) {
        
    }
    
    protected function _log($str){
        Mage::helper('orbaallegro/debug')->log($str);
    }
}