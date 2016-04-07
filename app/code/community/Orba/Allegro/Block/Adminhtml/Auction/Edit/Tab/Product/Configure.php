<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Product_Configure
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    
    public function __construct() {
        $this->setTemplate('orbaallegro/auction/edit/tab/configure.phtml');
		$this->setId('orbaallegro_auction_config_super_product');
        parent::__construct();
    }
	
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setAttributes('".$this->getContinueUrl()."')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }
	
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'			=> true,
			'super_attribute'	=> '{{attributes}}'
        ));
    }
	
	public function getContinueUrlButtonHtml() {
		return $this->getChildHtml('continue_button');
	}	

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
			$skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                if ($product->getIsInStock() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
	
    /**
     * Returns additional values for js config, con be overriden by descedants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        $result = parent::_getAdditionalConfig();
        $result['disablePriceReload'] = true; // There's no field for price at popup
        $result['stablePrices'] = true; // We don't want to recalc prices displayed in OPTIONs of SELECT
        return $result;
    }
}