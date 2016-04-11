<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;
$installer->startSetup();

$attribute = array();
$attribute[] = array('code' => 'tim_tytul_aukcji', 'label' => 'Tytul aukcji', 'type' => 'text');
$attribute[] = array('code' => 'tim_kategoria_rozmiaru', 'label' => 'Kategoria rozmiaru', 'type' => 'text');

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

foreach($attribute as $key => $attr){
    $attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attr['code']);

    if ($attrIdTest === false) {
        $arr = array(
            'group' => 'Tim basic',
            'type' => $attr['type'],
            'backend' => '',
            'frontend' => '',
            'label' => $attr['label'],
            'input' => $attr['type'],
            'class' => '',
            'source' => '',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'frontend_input' => '',
            'frontend_class' => '',
            'is_comparable' => 0,
            'visible' => '1',
            'required' => false,
            'user_defined' => '1',
            'default' => '',
            'is_visible_on_front' => 0,
            'is_unique' => 0,
            'is_configurable' => '1',
            'is_filterable' => '1',
            'is_filterable_in_search' => '1',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => 0,
            'is_used_for_promo_rules' => 0,
            'position' => '0',
            'is_html_allowed_on_front' => '1',
            'used_in_product_listing' => 0
        );

        $objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attr['code'], $arr);
    }

}
$installer->endSetup();