<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Extra extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => 'setLocation(\''. $this->getContinueUrl() . '\' )',
                    'class'     => 'save'
                ))
        );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {

        $editMode = $this->getEditMode();

        $store = $this->getStore();
        /* @var $store Mage_Core_Model_Store */

        $user = $this->getUser();
        /* @var $store Orba_Allegro_Model_User */

        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        $template = Mage::registry('template');
        /* @var $product Mage_Catalog_Model_Template */

        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */

        $config = Mage::getSingleton("orbaallegro/auction_config");
        /* @var $config Orba_Allegro_Model_Auction_Config */
        $countryCode = $config->getCountryCode($store);

        // Init client
        $client->setData($config->getLoginData($store));

        $form = Mage::getModel("orbaallegro/form_auction", array('country_code'=>$countryCode));
        /* @var $form Orba_Allegro_Model_Form_Auction */

        $form->setClient($client);
        $form->setCountryCode($countryCode);
        $form->setEditMode($editMode);
        $form->load($this->getCategory()->getExternalId());

        $productId = Mage::registry('product_id');
        $parentProductId = Mage::registry('parent_product_id');

        if ($productId && $parentProductId) {
            $productModel = Mage::getModel('catalog/product');
            $product = $productModel->load($productId);
        }

        $form->prepareConfigValues($config, $product, $store);
        $form->prepareTemplateValues($template);
        $form->prepareFilteredValues($product, $store, $user);

        // Fill category
        if($catField = $form->getField($form::FIELD_CATEGORY)){
            $catField->setShowExternalValue(true);
            $catField->setCountryCode($countryCode);
            $catField->setValue($this->getCategory()->getId());
        }

        // Fill shop category
        if($shopCatField = $form->getField($form::FIELD_SHOP_CATEGORY)){
            $shopCatField->setShowExternalValue(true);
            $shopCatField->setCountryCode($countryCode);
            $shopCatField->setIsAuctionForm(true);
            $id = $this->getShopCategory()->getId();
            $shopCatField->setValue(($id == null) ? 0 : $id);
        }

        $this->setForm($form);
    }

    protected function _getCountryCode() {
        $config = Mage::getSingleton('orbaallegro/config');
        /* @var $config Orba_Allegro_Model_Config */
        return $config->getCountryCode($this->getStore());
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'         => true,
            'template'      => Mage::registry('allegro_template_id'),
            'category'      => Mage::registry('allegro_category_id'),
            'shop_category' => Mage::registry('allegro_shop_cat_id'),
            'extra_done'    => '1',
        ));
    }

    protected function _getValues() {
        $values = array();
        $product = $this->getProduct();
        if($product && $product->getId()){
            if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY)){
                $values['category_id'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY);
            }
            if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY)){
                $values['shop_category_id'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY);
            }
            if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE)){
                $values['template_id'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
            }
        }
        return $values;
    }
}
