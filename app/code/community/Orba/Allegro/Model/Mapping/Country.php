<?php
/**
 * @todo move to DB
 * @todo add another countries
 */

class Orba_Allegro_Model_Mapping_Country{
    
    protected $_map = array(
        1   => "PL",
        228 => "PL"
    );
    
    public function getFullMap() {
        return $this->_map;
    }
    public function getCountryMapped($code) {
        if(isset($this->_map[$code])){
            return $this->_map[$code];
        }
        return null;
    }
}
