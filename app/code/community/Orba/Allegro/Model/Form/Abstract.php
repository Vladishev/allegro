<?php
/**
 * Klasa ma na celu budowy laskiego formularza z odpowieni kontorlkami
 */
abstract class Orba_Allegro_Model_Form_Abstract extends Varien_Data_Form {
    
    protected $_clientMethod;
    protected $_clientMethodArgs;
    protected $_parserName = "orbaallegro/form_parser_in";
    protected $_fields;
    protected $_parser;

    /**
     * Collect data for form fields in view 'external id' => 'type of field'
     *
     * External id - id of this field on Allegro servise
     * Type of field - numeric view of types(1 - string, 2 - integer...)
     *
     * @var array
     */
    protected $_res;

    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        
        $fieldset = $this->addFieldset("general", array("legend"=>Mage::helper("orbaallegro")->__("Auction parmeters")));
        
        $fieldset->addType("orbaallegro_category", 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_category_renderer'));
        
        $fieldset->addType("orbaallegro_shop_category", 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_shop_category_renderer'));
        
        $fieldset->addType("orbaallegro_country", 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_auction_country_renderer'));
        
        $fieldset->addType("image", 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_auction_image_renderer'));
        
        $fieldset->addType("orbaallegro_editor", 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_template_helper_form_wysiwyg'));
        
        $this->_parser = Mage::getSingleton($this->_parserName, array($this));
        $this->_fieldset = $fieldset;
    }
    
    public function load() {
        $data = call_user_func(
            array($this->getClient(), $this->_clientMethod),
            $this->_clientMethodArgs
        );
        if($data){
            $this->_processData($this->_extract($data));
            $this->setIsLoaded(1);
        }
        return $this;
    }
    
    /**
     * Zwraca tablice (idAllegro=>pole);
     */
    public function getFlatFields() {
        return $this->_fields;
    }

    /**
     * Returns array with types of fields
     *
     * @return mixed
     */
    public function getResData()
    {
        return $this->_res;
    }
    
    /**
     * @return Orba_Allegro_Model_Client
     * @throws Orba_Allegro_Exception
     */
    public function getClient() {
        if(!$this->getData('client')){
            throw new Orba_Allegro_Exception("No client to generate form");
        }
        return $this->getData('client');
    }
    
    protected function _processData($items){
        if(is_object($items)){
            $items = array($items);
        }
		$items = $this->_sortItems($items);

        $fields = array();
        
        foreach ($items as $item) {
            $field = $this->_processField($item);
            if($field instanceof Varien_Data_Form_Abstract){
                $this->_res[$item->sellFormId] = $item->sellFormResType;
                $fields[$item->sellFormId] = $field;
            }
        }
        $this->_fields = $fields;
        $this->addFieldNameSuffix($this->getPrefix());
    }
    
    protected function _processField($item){
        $type = $this->getParser()->parseType($item);
        $config = $this->getParser()->parseFieldConfig($item);
        $field = $this->_fieldset->addField($this->getPrefix().$item->sellFormId, $type, $config);
        $this->_addMetaFields($item);
        return $field;
    }
    
    protected function _addMetaFields($item) {
        return $this;
    }    
    
    public function getParser() {
        return $this->_parser;
    }
    
    protected function _extract($data) {
        return $data;
    }
    
    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _getElement($id) {
        return $this->getElement($this->getPrefix().$id);
    }
    
    protected function _sortItems($items) {
		return $items;
	}

}