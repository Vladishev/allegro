<?php
/**
 * @todo Implement
 */
class Orba_Allegro_Model_Form_Validator_Auction implements Zend_Validate_Interface{
    
    protected $_messages;
    
    public function isValid($value) {
        return true;
    }
    
    public function getMessages() {
        return $this->_messages;
    }
    
}