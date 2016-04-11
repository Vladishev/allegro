<?php
abstract class Orba_Allegro_Model_Abstract extends Mage_Core_Model_Abstract
{
    const DEFAULT_STORE_ID = 0;
    

    /**
     * @var array
     */
    protected $_defaultValues = array();

    /**
     * @var array
     */
    protected $_storeValuesFlags = array();

    
    /**
     * @return Orba_Allegro_Model_Config
     */
    protected function _getConfig() {
        return Mage::getModel('orbaallegro/config');
    }
    
    
    
    /**
     * @param   string $attributeCode
     * @value   mixed  $value
     * @return  Mage_Catalog_Model_Abstract
     */
    public function setAttributeDefaultValue($attributeCode, $value)
    {
        $this->_defaultValues[$attributeCode] = $value;
        return $this;
    }
    /**
     * Set attribute code flag if attribute has value in current store and does not use
     * value of default store as value
     *
     * @param   string $attributeCode
     * @return  Mage_Catalog_Model_Abstract
     */
    public function setExistsStoreValueFlag($attributeCode)
    {
        $this->_storeValuesFlags[$attributeCode] = true;
        return $this;
    }

    /**
     * Check if object attribute has value in current store
     *
     * @param   string $attributeCode
     * @return  bool
     */
    public function getExistsStoreValueFlag($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_storeValuesFlags);
    }
    
    /**
     * Retrieve default value for attribute code
     *
     * @param   string $attributeCode
     * @return  array|boolean
     */
    public function getAttributeDefaultValue($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_defaultValues) ? $this->_defaultValues[$attributeCode] : false;
    }
    
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }
    
    public function getStoreId() {
        if(!$this->hasData("store_id")){
            $this->setData("store_id", self::DEFAULT_STORE_ID);
        }
        return $this->getData("store_id");
    }

}