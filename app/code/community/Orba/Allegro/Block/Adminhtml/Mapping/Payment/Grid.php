<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Payment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function _construct() {
        parent::_construct();
        $this->setId('paymentGrid');
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
        
        $this->addColumn('payment_id', array(
            'header' => Mage::helper('orbaallegro')->__('Manual map'),
            'type'   => 'checkbox',
            'index'  => 'payment_id',
            'align'  => 'center',
            'values' => $this->_getSelectedIds()
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('orbaallegro')->__('Payemnt title'),
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
        
        $this->addColumn('payment_map', array(
            'header' => Mage::helper('orbaallegro')->__('Local method'),
            'index' => 'payment_map',
            'type' => 'select',
            'options' => $this->_getPaymentMethods(),
            'editable' => true,
            'filter' => false,
            'width' => "200px"
        ));
        
        
        return parent::_prepareColumns();
    }
    
    protected function _getSupportedServices() {
        if(!$this->getData("supported_services")){
            $this->setData("supported_services", Mage::getSingleton('orbaallegro/mapping_payment_source_service')->toOptionHash());
        }
        return $this->getData("supported_services");
    }
    
    protected function _getPaymentMethods() {
        if(!$this->getData("payment_methods")){
            $payments = Mage::getSingleton("payment/config")->getActiveMethods(); /* getAllMethods */
            $data = array();
            foreach($payments as $code=>$method){
                /* @var $method Mage_Payment_Model_Method_Abstract */
               if($code=="orbaallegro"){
                   continue;
               }
               $data[$code] = $method->getTitle();
            }
            $this->setData("payment_methods", $data);
        }
        return $this->getData("payment_methods");
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
        
        if(is_array($this->getReloadPaymentMap())){
            return $this->getReloadPaymentMap();
        }
        return array_keys($this->getPaymentMap());
    }


    public function getPaymentMap()
    {
        $collection = $this->_getCollection();
        $collection->addFieldToFilter("payment_map", array("notnull"=>true));
        $collection->addFieldToFilter("payment_map", array("neq"=>''));
        $payments = array();
        foreach ($collection as $payment) {
            $payments[$payment->getId()] = array('payment_map' => $payment->getPaymentMap());
        }
        return $payments;
    }
    
    protected function _getCollection() {
        $collection = Mage::getResourceModel('orbaallegro/mapping_payment_collection');
        /* @var $collection Orba_Allegro_Model_Resource_Payment_Collection */
        $collection->addFieldToFilter("country_code", 
                array("in"=>array_keys($this->_getSupportedServices())));
        return $collection;
    }
    

}