<?php
class Orba_Allegro_Block_Adminhtml_Auction_Bid_View extends Mage_Adminhtml_Block_Widget {

    /**
     *  @return Orba_Allegro_Model_Auction_Bid
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_auction_bid');
    }

    protected function _prepareLayout() {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Back'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class' => 'back'
                ))
        );  
        
        $this->setChild('ignore',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Ignore'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*/ignore', array("_current"=>true, 'mode'=>1)) . "'",
                    'class' => 'delete'
                ))
        );  
        
        $this->setChild('unignore',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Unignore'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*/ignore', array("_current"=>true, 'mode'=>0)) . "'",
                ))
        );  
        
        $this->setChild('refresh',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Refresh'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*/refresh', array("_current"=>true)) . "'",
                ))
        );  
        
        $unhandled = $this->getModel()->getUnhandledItemCount();

        if($unhandled>0){
            $placeParams = array(
                "bid_id"    => $this->getModel()->getId(),
                "quantity"  => $unhandled
            );

            $this->setChild('create_order_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('orbaallegro')->__('Place order'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*/createOrder', $placeParams) . "'",
                        'class' => 'add'
                    ))
            );
        }
        
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml() {
        return $this->getChildHtml('back_button');
    }
    
    public function getHasOrder() {
        return (bool)$this->getModel()->getOrder();
    }
    
    public function getIsIgnored() {
        return (bool)$this->getModel()->getIsIgnored();
    }
    
    public function getRefreshButton() {
        return $this->getChildHtml('refresh');
    }
    
    public function getIgnoreButton() {
        return $this->getChildHtml('ignore');
    }
    
    public function getUnIgnoreButton() {
        return $this->getChildHtml('unignore');
    }
    
    public function getCreateOrderButton() {
        return $this->getChildHtml('create_order_button');
    }

    public function getHeaderText() {
        return  Mage::helper('orbaallegro')->__('View bid from %s', $this->getModel()->getBuyerLogin());
    }
    
    public function getCountryLabel($coutryCode) {
        return Mage::helper('orbaallegro')->getCountryLabel($coutryCode);
    }
    
    public function getContractor() {
        return $this->getModel()->getContractor();;
    }
    
}
