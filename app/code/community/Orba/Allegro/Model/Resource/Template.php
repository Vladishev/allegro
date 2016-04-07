<?php
class Orba_Allegro_Model_Resource_Template extends Orba_Allegro_Model_Resource_Abstract{ 
    public function __construct()
    {
        parent::__construct();
        $this->setType(Orba_Allegro_Model_Template::ENTITY)
             ->setConnection('catalog_read', 'catalog_write');
        
    }
    
    protected function _getDefaultAttributes()
    {
        return array('entity_id', 'entity_type_id', 'attribute_set_id', 'country_code', 'created_at', 'updated_at');
    }

    protected function _beforeSave(Varien_Object $object) {
        // One attribute set per one template 
        if(!$object->getId()){
            
            $service = Mage::getModel("orbaallegro/service")->
                    load($object->getCountryCode(), "service_country_code");
            
            $newSet = Mage::getModel("eav/entity_attribute_set");
            /* @var $newSet Mage_Eav_Model_Entity_Attribute_Set */
            
            $newSet->setAttributeSetName($service->getServiceName());
            $newSet->setEntityTypeId($this->getEntityType()->getId());
            $newSet->save();
            $newSet->setAttributeSetName($newSet->getAttributeSetName() . " " . $newSet->getId());
            $newSet->initFromSkeleton($object->getDefaultAttributeSetId());
            $newSet->save();
            $object->setAttributeSetId($newSet->getId());
        }
        return parent::_beforeSave($object);
    }
    
    protected function _afterDelete(Varien_Object $object) {
        // Delete set with template
        Mage::getModel("eav/entity_attribute_set")->
            load($object->getAttributeSetId())->delete();
        parent::_afterDelete($object);
    }

    
}