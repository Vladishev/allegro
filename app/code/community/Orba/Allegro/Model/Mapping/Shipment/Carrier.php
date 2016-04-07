<?php
class Orba_Allegro_Model_Mapping_Shipment_Carrier extends 
        Mage_Shipping_Model_Carrier_Abstract implements 
        Mage_Shipping_Model_Carrier_Interface{
            
    protected $_code = "orbaallegro";
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        
        $result = Mage::getModel('shipping/rate_result');
        
        if(!count($request->getAllItems())){
            return $result;
        }
        
        //Mage::log("Has item");
        
        $item = current($request->getAllItems());
        /* @var $item Mage_Sales_Model_Quote_Item */
            
        $quote = $item->getQuote();
        
        if(is_null($shipmentId = $quote->getOrbaallegroShipmentId())){
            return $result; 
        }
        
       // Mage::log("has shipment id");
        
        if(is_null($transactionId = $quote->getOrbaallegroTransactionId())){
            return $result; 
        }
        
        //Mage::log("has transaction id");

        $shipment = Mage::getModel('orbaallegro/mapping_shipment')
                ->load($shipmentId);
        
        if(!$shipment->getId()){
            return $request;
        }
        
        //Mage::log("has shipment");
        
        $transaction = Mage::getModel("orbaallegro/transaction")->load($transactionId);
        
        if(!$transaction->getId()){
            return $request;
        }
        
        //Mage::log("has transaction");
        
            
        $shippingPrice = $transaction->getTransactionCodAmount();
        
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod('method'.$shipment->getAllegroShipmentId());
        $method->setMethodTitle($shipment->getName());
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        

        $result->append($method);

        

        return $result;
    }

    public function getAllowedMethods() {
        if(!$this->getData('allowed_methods')){
            $collection = Mage::getResourceModel("orbaallegro/mapping_shipment_collection");
            /* @var $collection Orba_Allegro_Model_Resource_Mapping_Shipment_Collection */
            $methods = array();
            foreach($collection as $shipment){
                $methods["method".$shipment->getAllegroShipmentId()] = $shipment->getName();
            }
            $this->setData('allowed_methods', $methods);
        }
        return $this->getData('allowed_methods');
    }
    
    
    public function isTrackingAvailable(){
        return false; /** @todo Impelment */
    }
    
    // Active only in admin
    public function isActive() {
        return Mage::app()->getStore()->isAdmin();
    }
}
