<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('orbaallegro/auction/edit.phtml');
        $this->setId('auction_edit');
    }

    /**
     * @return Orba_Allegro_Model_Auction
     */
    public function getAuction()
    {
        return Mage::registry('auction');
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Back'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class' => 'back'
                ))
        );
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Save and publish'),
                    'onclick' => 'auctionControl.save();',
                    'class' => 'save'
                ))
        );
        $this->setChild('test_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Test before save'),
                    'onclick' => 'auctionControl.test();'
                ))
        );
        $this->setChild('refresh_transaction',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Refresh transactions'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*/refreshTransactions', array("_current"=>true)) . "'",
                ))
        );
        $this->setChild('cancel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Cancel auction'),
                    'onclick' => 'auctionControl.remove();',
                    'class' => 'delete'
                ))
        );
        $this->setChild('mass_auctions',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Create auctions'),
                    'onclick' => 'auctionControl.create();',
                ))
        );

        return parent::_prepareLayout();
    }

    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('mass_auctions');
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('cancel_button');
    }
    
    public function getRefreshTransactionsButton()
    {
        return $this->getChildHtml('refresh_transaction');
    }

    public function getTestSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
    public function getTestButtonHtml()
    {
        return $this->getChildHtml('test_button');
    }

    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl(
                '*/*/save'.($this->getEditMode() ? "" : "New"),
                array('_current'=>true, 'back'=>null)
         );
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'         => true,
            'template'      => Mage::registry('allegro_template_id'),
            'category'      => Mage::registry('allegro_category_id'),
            'shop_category' => Mage::registry('allegro_shop_cat_id'),
            'extra_done'    => '1',
        ));
    }

    public function checkMassAction()
    {
        if (Mage::getSingleton('core/session')->getPostRequest()) {
            return true;
        }

        return false;
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }
    public function getTestUrl() {
        return $this->getUrl('*/*/test' . ($this->getEditMode() ? "" : "New"), array('_current'=>true));
    }
    
    public function getCanSave() {
        if($this->getAuction()->getId()){
              return $this->getAuction()->getCanModify();
        }
        return Mage::registry('store')->getId() && 
               Mage::registry('category')->getId() && 
               Mage::registry('product')->getId();
    }

    public function getWasPlaced() {
        return $this->getAuction()->getWasPlaced();
    }
    
    public function getHeaderText() {
        // Title for edit
        if ($this->getEditMode()) {
            $auction = $this->getAuction();
            $prefix = "#" . $auction->getAllegroAuctionId();
            $name = $this->escapeHtml($auction->getAuctionTitle());
            $service = $this->escapeHtml($auction->getService()->getServiceName());
            
        }else{
            $prefix = $header = Mage::helper('orbaallegro')->__('New auction');
            $name = "";
            $service = "";
            
            if(Mage::registry('product')->getId()){
                $name = $this->escapeHtml(Mage::registry('product')->getName());
            }
            if(Mage::registry('store')->getId()){
                $config = Mage::getModel('orbaallegro/config');
                /* @var $config Orba_Allegro_Model_Config */
                $cc = $config->getCountryCode(Mage::registry('store'));

                $serviceModel = Mage::getModel("orbaallegro/service")->load($cc, "service_country_code");
                if($serviceModel->getId()){
                    $service = $this->escapeHtml($serviceModel->getServiceName());
                }
            }

        }
        
        $out = array();
        
        if($prefix){
            $out[] = $prefix;
        }
        if($name){
            $out[] = $name;
        }
        if($service){
            $out[] =  $service;
        }
        
        return trim(implode(" / ", $out));
        
    }


    public function getEditMode() {
        return $this->getAuction()->getId();
    }
    
    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}
