<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();
$attribute  = array(
    'type'          =>  'text',
    'label'         =>  'Tim Category Id',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  true,
    'user_defined'  =>  true,
    'default'       =>  '',
    'group'         =>  'General Information'
);
$installer->addAttribute('catalog_category', 'tim_category_id', $attribute);
$installer->endSetup();