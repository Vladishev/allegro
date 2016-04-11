<?php
class Orba_Allegro_Block_Adminhtml_Auction_Grid_Column_Renderer_Timetoend 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
        
    protected $_services = array();
    
    public function render(Varien_Object $row)
    {
        if($row->isFinishedByUser()){
            return "";
        }
            
        if($row->getEndTime() && $row->getAllegroStartingTime()){
            $now = new Zend_Date();
            $stopTime = new Zend_Date($row->getEndTime());
            
            if($stopTime->compare($now)<1){
                return Mage::helper("orbaallegro")->__("Finished");
            }
            $diff = $stopTime->sub($now)->toValue();
            
            $str = "";
            if($diff>60*60*24){
                $diff = floor($diff/60/60/24);
                $str = "day";
                if($diff>1){
                    $str.="s";
                }
            }elseif($diff>60*60){
                $diff = floor($diff/60/60);
                $str = "hour";
                if($diff>1){
                    $str.="s";
                }
            }elseif($diff>60){
                $diff = floor($diff/60);
                $str = "minute";
                if($diff>1){
                    $str.="s";
                }
            }
             
            return  $diff . " " . Mage::helper("orbaallegro")->__($str);;
        }
        return "";
    }
}
