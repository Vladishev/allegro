<?php

class Orba_Allegro_Block_Template_Attributes extends Orba_Allegro_Block_Template_Abstract
{
    protected $_product = null;
   
    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                
                $value = $attribute->getFrontend()->getValue($product);
                $prodValue = $product->getData($attribute->getAttributeCode());
                
                if (!$product->hasData($attribute->getAttributeCode())) {
                    continue;
                } elseif ($prodValue===null || $prodValue==="") {
                    continue;
                } elseif ((string)$value == '') {
                    continue;
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode()
                    );
                }
            }
        }
        return $data;
    }
}
