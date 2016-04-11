<?php
class Orba_Allegro_Model_System_Config_Source_Service extends Orba_Allegro_Model_System_Config_Source_Abstract{
    
   public function toOptionArray() { 
       foreach($this->toOptionHash() as $k=>$v){
           $array[] = array('label' => $v, 'value' => $k);
       }
       return $array;
   }
   
   public function toOptionHash() { 
       $service = Mage::getSingleton("orbaallegro/service");
       $data = Mage::helper('orbaallegro')->isFirstImportComplete() ? 
            $service->getCountryCodesToLabel() :     // Before import data
            $service->getConstCountryCodesToLabel(); // After importa data
       
       $array = array();
       foreach($data as $k=>$v){
           $array[$k] =  $v;
       }
       return $array;
   }
   
}