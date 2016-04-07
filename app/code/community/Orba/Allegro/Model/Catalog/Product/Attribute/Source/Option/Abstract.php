<?php

/**
 * Description of Abstract
 */
abstract class Orba_Allegro_Model_Catalog_Product_Attribute_Source_Option_Abstract 
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
    

    
    public function getFlatColums() {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );
        $helper = Mage::helper('core');
        if (!method_exists($helper, 'useDbCompatibleMode') || $helper->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
        }
        return array($attributeCode => $column);
    }
    
    public function getFlatUpdateSelect($store) {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
?>
