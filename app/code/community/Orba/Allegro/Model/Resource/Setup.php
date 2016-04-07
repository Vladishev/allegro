<?php
class Orba_Allegro_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
       
    protected $_supportedServices = array(
        Orba_Allegro_Model_Service::ID_ALLEGROPL,
        Orba_Allegro_Model_Service::ID_WEBAPI
    );
    
    public function getSupportedServices() {
        return $this->_supportedServices;
    }
    
    /**
     * Prepare Allegro attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'frontend_input_renderer'       => $this->_getValue($attr, 'frontend_input_renderer'),
            'global'                        => $this->_getValue($attr, 'global', Orba_Allegro_Model_Resource_Eav_Attribute::SCOPE_GLOBAL),
            'is_visible'                    => $this->_getValue($attr, 'visible', 1),
            'position'                      => $this->_getValue($attr, 'position', 0),
            'is_wysiwyg_enabled'            => $this->_getValue($attr, 'is_wysiwyg_enabled', 0),
            'frontend_input_renderer'       => $this->_getValue($attr, 'input_renderer'),
            // Allegro metadata
            'allegro_used'                  => $this->_getValue($attr, 'allegro_used', 0),
            'allegro_form_id'               => $this->_getValue($attr, 'allegro_form_id'),
            'allegro_param_id'              => $this->_getValue($attr, 'allegro_param_id', 0),
            'allegro_type'                  => $this->_getValue($attr, 'allegro_type'),
            'allegro_meta'                  => $this->_getValue($attr, 'allegro_meta'),
            'allegro_apply_to'              => $this->_getValue($attr, 'allegro_apply_to'),
        ));
        return $data;
    }
    
    
    /**
     * Overridden for add custom function
     * @param array|null $entities
     * @return Orba_Allegro_Model_Resource_Setup
     */
    public function installEntities($entities = null) {
        $ret = parent::installEntities($entities);
        $this->_installEavComponents();
        return $ret;
    }


    /**
     * Default entites and attributes
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'orbaallegro_template'                => array(
                'entity_model'                   => 'orbaallegro/template',
                'attribute_model'                => 'orbaallegro/resource_eav_attribute',
                'table'                          => 'orbaallegro/template',
                'additional_attribute_table'     => 'orbaallegro/eav_attribute',
                'entity_attribute_collection'    => 'orbaallegro/template_attribute_collection',
                'attributes'                     => array(
                    'created_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'label'                      => 'Name',
                        'backend'                    => 'eav/entity_attribute_backend_time_created',
                        'visible'                    => false,
                    ),
                    'updated_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'label'                      => 'Name',
                        'backend'                    => 'eav/entity_attribute_backend_time_updated',
                        'visible'                    => false, 
                    ),
                    'name'               => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Internal name',
                        'input'                      => 'text',
                        'position'                   => 10,
                        'global'                     => Orba_Allegro_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'group'                      => Orba_Allegro_Model_Template::GROUP_GENERAL,
                    ),
                    'status'             => array(
                        'type'                       => 'int',
                        'label'                      => 'Internal status',
                        'input'                      => 'select',
                        'position'                   => 20,
                        'source'                     => "orbaallegro/template_status",
                        'default'                    => Orba_Allegro_Model_Template_Status::STATUS_ENABLED,
                        'global'                     => Orba_Allegro_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'group'                      => Orba_Allegro_Model_Template::GROUP_GENERAL,
                    )
                )
            )
        );
    }

    /**
     * Import data - user is logged! Login data stroed in config per store!
     * @return boolean
     */
    public function doFirstImport() {
        $helper = Mage::helper('orbaallegro');
        if($helper->isFirstImportComplete()){
            return false;
        }
        $this->importAndMapLatestSeveices();
        $this->importLatestShipments();
        $this->importShopCategories();
        $helper->setFirstImportComplete(); 
    }
    
    public function importShopCategories() {
        $categoryModel = Mage::getModel('orbaallegro/shop_category');
        /* @var $categoryModel Orba_Allegro_Model_Shop_Category */
        $categoryModel->doImport();
        return true;
    }

    public function installDefualtServices() {
        $serviceModel = Mage::getModel("orbaallegro/service");
        /* @var $serviceModel Orba_Allegro_Model_Service */
        $constCodes = $serviceModel->getAllConstCountryCodes();
        $labels = $serviceModel->getConstCountryCodesToLabel();
        $currencies = $serviceModel->getConstCountryCodesToCurrency();
        
        foreach($constCodes as $countryCode){
            
            $model = Mage::getModel("orbaallegro/service");
            
            $code = $serviceModel->mapCountryCodeToCode($countryCode);
            $label = $labels[$countryCode];
            $currency = $currencies[$countryCode];
            
            $model->setData(array(
                'is_production'         => (int)$countryCode!=Orba_Allegro_Model_Service::ID_WEBAPI,
                'is_supported'          => (int)in_array($countryCode, $this->_supportedServices),
                'service_code'          => $code,
                'service_country_code'  => $countryCode,
                'service_currency'      => $currency,
                'service_name'          => $label
            ));
            
            $model->save();
        }
        $this->_installDefaultAttributeSets();
    }
    
    /**
     * Import full data by local constansts
     */
    public function importAndMapLatestSeveices() {
        $serviceModel = Mage::getModel("orbaallegro/service");
        /* @var $serviceModel Orba_Allegro_Model_Service */
        $coll = Mage::getResourceModel('orbaallegro/service_collection');


        $client = Mage::getModel('orbaallegro/client');
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId(Mage::app()->getStore()->getId());
        $client->setCountryCode(Orba_Allegro_Model_Service::ID_ALLEGROPL);

        $resultArray = array();
        $verKeyArray = array();
        $verStrArray = array();

        $constCodes = $serviceModel->getAllConstCountryCodes();

        foreach ($constCodes as $countryCode) {
            try {
                $result = $client->getSitesInfo($countryCode);
            } catch (Exception $e) {
                Mage::Log('[Orba_Allegro] ' . $e->getMessage());
                continue;
            }
            if (isset($result->sitesInfoList) && isset($result->sitesInfoList->item)) {
                $items = $result->sitesInfoList->item;
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $siteCountryCode = (int) $item->siteCountryCode;
                        if (!isset($resultArray[$siteCountryCode])) {
                            $resultArray[$siteCountryCode] = $item;
                        }
                    }
                } else {
                    $siteCountryCode = (int) $items->siteCountryCode;
                    if (!isset($resultArray[$siteCountryCode])) {
                        $resultArray[$siteCountryCode] = $items;
                    }
                }
            }
            if (isset($result->verKey)) {
                $verKeyArray[$countryCode] = (string) $result->verKey;
            }
            if (isset($result->verStr)) {
                $verStrArray[$countryCode] = (string) $result->verStr;
            }
        }

        foreach ($coll as $service) {
            if (!$service->getInstalledFromApi()) {
                $countryCode = $service->getServiceCountryCode();
                if (isset($resultArray[$countryCode])) {
                    $serviceData = $resultArray[$countryCode];
                    $service->addData(array(
                        'service_code_page' => (string) $serviceData->siteCodePage,
                        'service_name' => (string) $serviceData->siteName,
                        'service_url' => (string) $serviceData->siteUrl,
                        'installed_from_api' => 1
                    ));
                }

                if (isset($verKeyArray[$countryCode])) {
                    $service->setData("version_key", $verKeyArray[$countryCode]);
                }

                if (isset($verStrArray[$countryCode])) {
                    $service->setData("version_list", $verStrArray[$countryCode]);
                }


                $service->save();
            }
        }


        return true;
    }
    
    public function importLatestPayments() {
        
       

    }


    public function importLatestShipments() {
        $client = Mage::getModel('orbaallegro/client');
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId(Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId());
        
        $coll = Mage::getResourceModel('orbaallegro/service_collection');
        /* @var $coll Orba_Allegro_Model_Resource_Service_Collection */
        
        $data = array();
        
        foreach ($coll as $service) {
            /* @var $service Orba_Allegro_Model_Service */
            $countryCode = $service->getServiceCountryCode();
            
            $serviceModel = Orba_Allegro_Model_Service::factory($countryCode);
            if($serviceModel){
                $pickpointProviders = $serviceModel->getAllPickpointProviders();
                if(count($pickpointProviders)){
                    $providers = array();
                    foreach ($pickpointProviders as $providerId){
                        $now = new Zend_Db_Expr("NOW()");
                        $providers[] = array(
                            'provider_name' => $serviceModel->getProviderNameById($providerId),
                            'country_code' => $countryCode,
                            'local_id' => $providerId,
                            'created_at' => $now,
                            'updated_at' => $now
                        );
                    }
                    $this->_conn->insertMultiple($this->getTable("orbaallegro/pickpoint_provider"), $providers);
                }
            }
      
            $result = false;
            try{
                $result = $client->getShipmentData($countryCode);
                usleep(50);
            }catch(Exception $e){
                Mage::logException($e);
                continue;
            }
            if(is_object($result) && isset($result->shipmentDataList)){
                if(isset($result->shipmentDataList->item)){
                    if(!is_array($result->shipmentDataList->item)){
                        $result->shipmentDataList->item = array($$result->shipmentDataList->item);
                    }
                }else{
                    $result->shipmentDataList->item = array();
                }
                
                foreach ($result->shipmentDataList->item as $shipment){    
                    $shipmentTimeFrom = null;
                    $shipmentTimeTo = null;
                    
                    if(isset($shipment->shipmentTime)){
                        if(isset($shipment->shipmentTime->shipmentTimeFrom)){
                            $shipmentTimeFrom = $shipment->shipmentTime->shipmentTimeFrom;
                        }
                        if(isset($shipment->shipmentTime->shipmentTimeTo)){
                            $shipmentTimeTo = $shipment->shipmentTime->shipmentTimeTo;
                        }
                    }
                    $isPickpoint = 0;
                    if($serviceModel)
                        $isPickpoint = (int)$serviceModel->checkIsPickpoint($shipment->shipmentId);
                    
                    $data[] = array(
                        "country_code" => $countryCode,
                        "allegro_shipment_id" => $shipment->shipmentId,
                        "name" => $shipment->shipmentName,
                        "type" => $shipment->shipmentType,
                        "time_from" => $shipmentTimeFrom,
                        "time_to" => $shipmentTimeTo,
                        "is_pickpoint" => $isPickpoint,
                        "created_at" => new Zend_Db_Expr("NOW()"),
                        "updated_at" => new Zend_Db_Expr("NOW()"),
                    );
                }
            }    
        }
        
        if(count($data)){
            $this->_conn->insertOnDuplicate($this->getTable('orbaallegro/mapping_shipment'), $data, 
                    array("name", "type", "time_from", "time_to", "is_pickpoint", "updated_at"));
        }
        
        return $this;
    }
    
    
    /**
     * Install default attribute set for services
     * @return boolean
     * @todo Implement
     */
    protected function _installDefaultAttributeSets() {
        $collection = Mage::getResourceModel("orbaallegro/service_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Service_Collection */
        $template = Mage::getResourceModel("orbaallegro/template");
        /* @var $template Orba_Allegro_Model_Template */
        $entityType = $template->getEntityType();
        /* @var $entityType Mage_Eav_Model_Entity_Type */
        
        $collection->addFieldToFilter("attribute_set_id", array("null"=>true));
        
        foreach ($collection as $service){
            $newSet = Mage::getModel("eav/entity_attribute_set");
            /* @var $newSet Mage_Eav_Model_Entity_Attribute_Set */
            $newSet->setAttributeSetName("Default " . $service->getServiceName());
            $newSet->setEntityTypeId($entityType->getId());
            $newSet->save();
            $newSet->setAttributeSetName($newSet->getAttributeSetName());
            $newSet->initFromSkeleton($entityType->getDefaultAttributeSetId());
            $newSet->save();
            // Set as default for service
            $service->setAttributeSetId($newSet->getId());
            $service->save();
        }
        return true;
    }
    
    
    
   /**
    * Install some addtional componetns
    * add new attr group
    */
   protected function _installEavComponents() {

       /////////////////////////////////////////////////////////////////////////
       // Orba Allegro EAV Groups
       ///////////////////////////////////////////////////////////////////////// 
       
       // Set sortorders - General
       $this->addAttributeGroup(
               'orbaallegro_template', 
               $this->getDefaultAttributeSetId(Orba_Allegro_Model_Template::ENTITY), 
               Orba_Allegro_Model_Template::GROUP_GENERAL,
               20
       );
       
       // Images
       $this->addAttributeGroup(
               'orbaallegro_template', 
               $this->getDefaultAttributeSetId(Orba_Allegro_Model_Template::ENTITY), 
               Orba_Allegro_Model_Template::GROUP_IMAGES,
               40
       );
       
       // Delivery paramters group for templates
       $this->addAttributeGroup(
               'orbaallegro_template', 
               $this->getDefaultAttributeSetId(Orba_Allegro_Model_Template::ENTITY), 
               Orba_Allegro_Model_Template::GROUP_DELIVERY,
               60
       );
       
       // Payment paramters group for templates
       $this->addAttributeGroup(
               'orbaallegro_template', 
               $this->getDefaultAttributeSetId(Orba_Allegro_Model_Template::ENTITY), 
               Orba_Allegro_Model_Template::GROUP_PAYMENT,
               80
       );
       
       // Cutstom paramters group for templates
       $this->addAttributeGroup(
               'orbaallegro_template', 
               $this->getDefaultAttributeSetId(Orba_Allegro_Model_Template::ENTITY), 
               Orba_Allegro_Model_Template::GROUP_CUSTOM,
               100
       );
       
       // Add special Attribute sets - on per service
       
        
       /////////////////////////////////////////////////////////////////////////
       // Magento Catalog components
       /////////////////////////////////////////////////////////////////////////
        
       $catalogInstaller = Mage::getResourceModel("catalog/setup", "core_setup");
       /* @var $catalogInstaller Mage_Catalog_Model_Resource_Setup */

       // Product use auto mapping
       $catalogInstaller->addAttribute(
            "catalog_product", 
             Orba_Allegro_Model_Mapping::ATTR_CODE_USE_MAPPING, 
             array(
               "type"              => "int",
               "input"             => "select",
               "label"             => "Use global mapping",
               "source"            => "eav/entity_attribute_source_boolean",
               "default"           => 1,
               "position"          => 900,
               "group"             => Mage::helper("orbaallegro")->__("Orba | Allegro")
           )
       );

       // Porduct Allegro category
       $catalogInstaller->addAttribute(
            "catalog_product", 
             Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY, 
             array(
               "type"              => "int",
               "input"             => "select",
               "label"             => "Category",
               "input_renderer"    => "orbaallegro/adminhtml_category_renderer",
               "position"          => 910,
               "global"            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
               "required"          => 0,
               "group"             => Mage::helper("orbaallegro")->__("ORBA | Allegro")
           )
       );
       
       // Porduct Allegro shop category
       $catalogInstaller->addAttribute(
            "catalog_product", 
             Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY, 
             array(
               "type"              => "int",
               "input"             => "select",
               "label"             => "Shop category",
               "input_renderer"    => "orbaallegro/adminhtml_shop_category_renderer",
               "position"          => 920,
               "global"            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
               "required"          => 0,
               "group"             => Mage::helper("orbaallegro")->__("ORBA | Allegro")
           )
       );
       
       // Product template
       $catalogInstaller->addAttribute(
            "catalog_product", 
             Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE,
             array(
               "type"              => "int",
               "input"             => "select",
               "label"             => "Defualt template",
               "source"            => "orbaallegro/catalog_product_attribute_source_option_template",
               "position"          => 930,
               "global"            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
               "required"          => 0,
               "group"             => Mage::helper("orbaallegro")->__("Orba | Allegro")
           )
       );
       
       // Product Youtube code
       $catalogInstaller->addAttribute(
            "catalog_product", 
             Orba_Allegro_Model_Config::ATTR_YOUTUBE_CODE,
             array(
               "type"              => "varchar",
               "input"             => "text",
               "label"             => "Youtube Movie ID",
               "position"          => 940,
               "global"            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
               "required"          => 0,
               "group"             => Mage::helper("orbaallegro")->__("Orba | Allegro")
           )
       );
       
       
       
    }
}
