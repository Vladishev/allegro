<?php
/**
 * Tab with flatform using for edit mode
 */
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Status
    extends Mage_Adminhtml_Block_Widget
{
    public function __construct() {
        $this->setTemplate('orbaallegro/auction/edit/tab/status.phtml');
        parent::__construct();
    }


    protected function _prepareLayout()
    {   
        $auction = $this->getAuction();
        parent::_prepareLayout();
    }
    
    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('_current'=>true));
    }
    
    public function getCanCancel() {
        return $this->getAuction()->getCanCancel();
    }
    
    public function getCancelForm() {
        
        $form = new Varien_Data_Form();
        
        
        $form->addField("cancel_bids", "checkbox", array(
            "name"          => "cancel[cancel_bids]",
            "value"         => 1
        ));
        
        $form->addField("cancel_reason", "text", array(
            "name"          => "cancel[cancel_reason]"
        ));
        
        
        return $form;
    }
    
    public function getCancelButton() {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        $button->setLabel(Mage::helper('orbaallegro')->__("Finish auction"));
        $button->setId("cancel_form_submit");
        $button->setType("button");
        $button->setOnclick("cancelControll.send();");
        return $button;
    }
    
    
    public function getCanSellAgain() {
        return $this->getAuction()->getCanSellAgain();
    }
    
    public function getSellAgainUrl()
    {
        return $this->getUrl('*/*/sellAgain', array('_current'=>true));
    }
    
    public function getSellAgainForm() {
        
        $form = new Varien_Data_Form();
        
        $form->addField("starting_time", "date", array(
            "name"          => "sellagian[starting_time]",
            "image"         => Mage::getDesign()->getSkinUrl("images/grid-cal.gif"),
            "format"        => $this->_getDateTimeFormat(),
            "input_format"  => $this->_getDateTimeFormat(),
            "time"          => true,
        ));

        return $form;
    }
    
    public function getSellAgainButton() {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        $button->setLabel(Mage::helper('orbaallegro')->__("Sell again"));
        $button->setId("sell_again_submit");
        $button->setType("button");
        $button->setOnclick("sellAgaiinControll.send();");
        return $button;
    }
    
    protected function _getDateTimeFormat() {
        return Mage::app()->getLocale()
            ->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
    
    public function getStatus($statusCode) {
        $statuses = Mage::getSingleton("orbaallegro/auction_status")->toOptionHash();
        /* @var $statuses Orba_Allegro_Model_Auction_Status */
        if(isset($statuses[$statusCode])){
            return $statuses[$statusCode];
        }
        return "";
    }
}
