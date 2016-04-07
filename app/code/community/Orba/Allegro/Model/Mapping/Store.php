<?php
/**
 * Add siores in service filter
 */

class Orba_Allegro_Model_Mapping_Store extends Mage_Adminhtml_Model_System_Store{
    public function getStoreValuesForForm($empty = false, $all = false, $countryCode=1)
    {
        
        $config = Mage::getModel('orbaallegro/config');
        /* @var $config Orba_Allegro_Model_Config */
        $storeIds = $config->getStoresByCountryCode($countryCode, $all);
        
        $collection = Mage::getModel('core/store')
                ->getCollection()
                ->setFlag('load_default_store', $all)
                ->addFieldToFilter('store_id', array('in'=>$storeIds));
                 
        /* @var $collection Mage_Core_Model_Resource_Store_Collection */
        
        $newAll = false;
        $this->_storeCollection = $collection->getItems();
        
        $adminId  = Mage_Core_Model_App::ADMIN_STORE_ID;
        
        if($all && $collection->getItemById($adminId)){
            $newAll = true;
        }
        
        $ret = parent::getStoreValuesForForm($empty, $newAll);
        
        if(count($ret) && $ret[0]['value']==$adminId){
            $ret[0]['label'] = Mage::helper('orbaallegro')->__('Default store values');
        }
        
        return $ret;
        
    }
}
