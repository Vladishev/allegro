<?php

abstract class Orba_Allegro_Model_Form_Parser_Abstract extends Varien_Object{
    const VALUE_REQUIRED = 1;

    const VALUE_TYPE_INPUT_TEXT = 1;
    const VALUE_TYPE_INPUT_NUMBER = 2;
    const VALUE_TYPE_INPUT_FLOAT = 3;
    const VALUE_TYPE_INPUT_DATETIME = 9;
    const VALUE_TYPE_INPUT_DATE = 13;
    const VALUE_TYPE_INPUT_RADIO = 5;
    const VALUE_TYPE_INPUT_CHECKBOX = 6;
    const VALUE_TYPE_INPUT_FILE = 7;
    const VALUE_TYPE_SELECT = 4;
    const VALUE_TYPE_TEXTAREA = 8;
    
    const XPATH_HINT_KEY = 'store_switcher';
    
    
    protected function _getDateTimeFormat() {
        return Mage::app()->getLocale()
            ->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
    
    protected function _getDateFormat() {
        return Mage::app()->getLocale()->getDateFormat();
    }
    
}