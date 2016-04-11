<?php
class Orba_Allegro_Model_Template extends Orba_Allegro_Model_Abstract{
    
    const ENTITY = "orbaallegro_template";
    
    const GROUP_GENERAL  = "General";
    const GROUP_DELIVERY = "Delivery";
    const GROUP_PAYMENT  = "Payment";
    const GROUP_CUSTOM   = "Custom";
    const GROUP_IMAGES   = "Images";
    
    
    protected function _construct() {
        parent::_construct();
        $this->_init("orbaallegro/template");
    }
 
    
    public function getDefaultAttributeSetId()
    {
        if($this->getCountryCode()){
            $model = Mage::getModel("orbaallegro/service")->load(
                    $this->getCountryCode(), "service_country_code"
            );
            if($attributeSetId=$model->getAttributeSetId()){
                return $attributeSetId;
            }
        }
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }
    
    /**
     * @param int  $groupId   Retrieve attributes of the specified group
     * @param bool $skipSuper Not used
     * @return array
     */
    public function getAttributes($groupId = null)
    {
        $productAttributes = $this->getSetAttributes();
        if ($groupId) {
            $attributes = array();
            foreach ($productAttributes as $attribute) {
                if ($attribute->isInGroup($this->getAttributeSetId(), $groupId)) {
                    $attributes[] = $attribute;
                }
            }
        } else {
            $attributes = $productAttributes;
        }

        return $attributes;
    }
    
    
    public function getSetAttributes()
    {
        return $this->getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes($this->getAttributeSetId());
    }
   
    
}
