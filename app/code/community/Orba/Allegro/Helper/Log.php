<?php
class Orba_Allegro_Helper_Log {
    
    const DEBUG_FILE = "allegro.log";
    const DEBUG_PREFIX = "allegro";
    const XML_PATH_IS_DEBUG_MODE = "orbaallegro/utils/debug_mode";
    
    /**
     * Log msg everytime when Orba_Allegro debug mode in on
     * @param string $msg
     * @param string|null $prefix
     */
    public function log($msg, $prefix=null) {
        if(Mage::helper("orbaallegro")->getIsDebugMode()){
            $this->_write($msg, $prefix, Zend_Log::INFO, self::DEBUG_FILE);
        }
    }
    
    public function dump($data, $file=null, $method=null) {
        
    }
    
    /**
     * 
     * @param string $msg
     * @param string|null $prefix
     * @param int|null $level
     */
    protected function _write($msg, $prefix=null, $level=null, $file=self::DEBUG_FILE) {
        if(!is_scalar($msg)){
            $msg = print_r($msg, 1);
        }
        
        if($this->isDebugMode()){
            $globalPrefix = self::DEBUG_PREFIX;
            
            if(is_string($prefix) && !empty($prefix)){
                $globalPrefix .= "[" . $prefix . "]";
            }
            $msg  = $globalPrefix. ": " . "\n" . str_repeat("-", 64) . "\n" . $msg . "\n\n";
            Mage::log($msg, $level, $file);
        }
    }
    
    /**
     * Is Debug Mode?
     * @return bool
     */
    public function isDebugMode() {
        return (bool)Mage::getStoreConfig(self::XML_PATH_IS_DEBUG_MODE);
    }
}
