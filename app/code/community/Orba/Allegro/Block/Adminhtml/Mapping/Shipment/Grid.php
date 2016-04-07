<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function _construct() {
        parent::_construct();
        $this->setId('shipmentGrid');
        $this->setDefaultSort('country_code');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        
        $collection = $this->_getCollection();
        $collection->addFieldToFilter("country_code", 
                array("in"=>array_keys($this->_getSupportedServices())));
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        
        $this->addColumn('shipment_id', array(
            'header' => Mage::helper('orbaallegro')->__('Manual map'),
            'type'   => 'checkbox',
            'index'  => 'shipment_id',
            'align'  => 'center',
            'values' => $this->_getSelectedIds()
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('orbaallegro')->__('Shimpment name'),
            'index' => 'name',
        ));
        
        $this->addColumn('country_code', array(
            'header' => Mage::helper('orbaallegro')->__('Allegro Service'),
            'align' => 'left',
            'index' => 'country_code',
            'type' => 'options',
            'options' => $this->_getSupportedServices(),
            'sortable' => true,
            'width' => "100px"
        ));
        
        $this->addColumn('shipment_map', array(
            'header' => Mage::helper('orbaallegro')->__('Local method'),
            'index' => 'shipment_map',
            'type' => 'select',
            'options' => $this->_getCarrierMethods(),
            'editable' => true,
            'filter' => false,
            'width' => "200px"
        ));
        
        
        return parent::_prepareColumns();
    }
    
    protected function _getSupportedServices() {
        if(!$this->getData("supported_services")){
            $this->setData("supported_services", Mage::getSingleton('orbaallegro/mapping_shipment_source_service')->toOptionHash());
        }
        return $this->getData("supported_services");
    }
    
    protected function _getCarrierMethods() {
        if(!$this->getData("carrier_methods")){
            $data = array();
            $carriers = Mage::getSingleton("shipping/config")->getActiveCarriers(); /* getAllCarriers */
            foreach($carriers as $carrier){
                
                if(!$carrier instanceof Mage_Shipping_Model_Carrier_Interface){
                    continue;
                }
                
                // Skip own currier
                if($carrier instanceof Orba_Allegro_Model_Mapping_Shipment_Carrier){
                    continue;
                }
                
                $methods = array();
                try{
                    $methods = $carrier->getAllowedMethods();
                }catch(Exception $e){
                    continue;
                }
                foreach($methods as $key=>$title) {
                    /* @var $carrier Mage_Shipping_Model_Carrier_Abstract */
                    if($title){
                        $data[$carrier->getCarrierCode() . "_" . $key] = $title;
                    }
                }
                
            }
            $this->setData("carrier_methods", $data);
        }
        return $this->getData("carrier_methods");
    }
    
    public function getRowUrl($row) {
        return "#";
    }
    
    public function getGridUrl() {
        return $this->getUrl("*/*/grid");
    }

    public function getHeaderCssClass() {
        return 'icon-head head-cms-page';
    }
    
    protected function _getSelectedIds() {
        
        if(is_array($this->getReloadShipmentMap())){
            return $this->getReloadShipmentMap();
        }
        return array_keys($this->getShipmentMap());
    }


    public function getShipmentMap()
    {
        $collection = $this->_getCollection();
        $collection->addFieldToFilter("shipment_map", array("notnull"=>true));
        $collection->addFieldToFilter("shipment_map", array("neq"=>''));
        $shipments = array();
        foreach ($collection as $shipment) {
            $shipments[$shipment->getId()] = array('shipment_map' => $shipment->getShipmentMap());
        }
        return $shipments;
    }
    
    protected function _getCollection() {
        $collection = Mage::getResourceModel('orbaallegro/mapping_shipment_collection');
        /* @var $collection Orba_Allegro_Model_Resource_Shipment_Collection */
        $collection->addFieldToFilter("country_code", 
                array("in"=>array_keys($this->_getSupportedServices())));
        return $collection;
    }
    

}