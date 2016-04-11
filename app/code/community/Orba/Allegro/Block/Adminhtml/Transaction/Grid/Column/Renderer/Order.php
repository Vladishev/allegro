<?php
class Orba_Allegro_Block_Adminhtml_Transaction_Grid_Column_Renderer_Order
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
        
    public function render(Varien_Object $row)
    {   
        if($row->getOrderId() && ($incrementId = $row->getIncrementId())){
            $model = Mage::getModel("sales/order")->load($row->getOrderId());
            if($model->getId()){
                return '<a href="'.$this->_getViewLink($model->getId()).'" >'.
                            $row->getIncrementId().
                        '</a>';
            }
        }
        return '<a href="'.$this->_getCreateLink($row->getId()).'">'.
                    Mage::helper("orbaallegro")->__("Place order").
               '</a>';
    }
    
    protected function _getCreateLink($transactionId){
        return $this->getUrl("*/transaction/createOrder", array("transaction"=>$transactionId));
    }
    
    protected function _getViewLink($orderId){
        return $this->getUrl("*/sales_order/view", array("order_id"=>$orderId));
    }
}
