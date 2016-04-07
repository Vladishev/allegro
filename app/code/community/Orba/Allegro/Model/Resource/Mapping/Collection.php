<?php
class Orba_Allegro_Model_Resource_Mapping_Collection extends 
    Mage_Core_Model_Resource_Db_Collection_Abstract {
    
        
    protected function _construct(){
        parent::_construct();
        $this->_init('orbaallegro/mapping');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }
    
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        if (!is_array($store)) {
            $store = array($store);
        }

        if ($withAdmin) {
            $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
        }

        $this->addFilter('store', array('in' => $store), 'public');

        return $this;
    }
    
    /**
     * @param bool $withAdmin
     * @return Orba_Allegro_Model_Resource_Mapping_Collection
     */
    
//    public function addCurrentServicesFilter($withAdmin = false) {
//        $collService = Mage::getModel('orbaallegro/service')->getCollection();
//        $collService->addAvailableFilter();
//
//        $config = Mage::getModel('orbaallegro/config');
//        /* @var $config Orba_Allegro_Model_Config */
//
//        foreach ($collService as $service) {
//            $countryCode = $service->getServiceCountryCode();
//            $stores[$countryCode] = $config->getStoresByCountryCode($countryCode);
//        }
//
//        $select = $this->getSelect();
//        $adapter = $select->getAdapter();
//
//        $select->
//            join(array(
//                'store' => $this->getTable('orbaallegro/mapping_store')), 
//                'store.mapping_id=main_table.mappng_id', 
//                array()
//            )->
//            group('main_table.mappng_id');
//
//        foreach ($stores as $countryCode => $stores) {
//            $stores = $adapter->quote($stores);
//            $countryCode = $adapter->quote($countryCode);
//            $select->orWhere(new Zend_Db_Exception(
//                            "(mapping.country_code=" . $countryCode . " AND " . "store.store_id IN (" . $stores . "))"
//            ));
//        }
//
//        return $this;
//    }
    
    public function addServiceName($alias="service_name") {
        $this->getSelect()->joinLeft(
                array('service'=>$this->getTable('orbaallegro/service')), 
                'service.service_country_code=main_table.country_code',
                array($alias)
        );
        return $this;
    }
    
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('orbaallegro/mapping_store')),
                'main_table.mapping_id = store_table.mapping_id',
                array()
            )->group('main_table.mapping_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }    
    
}
