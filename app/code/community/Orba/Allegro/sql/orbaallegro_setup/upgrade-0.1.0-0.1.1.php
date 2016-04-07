<?php
/* v.0.1.1 - Repair module installation */

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();

$config = Mage::getModel('core/config_data')
            ->getCollection()
            ->addFieldToFilter('path', 'orbaallegro/config/is_import_complete')
            ->addFieldToFilter('scope', Mage_Core_Model_Store::DEFAULT_CODE)
            ->getFirstItem();

if($config->getConfigId() && $config->getValue() == 1){
    $setup = Mage::getResourceModel("orbaallegro/setup", "core_setup");
    $setup->importAndMapLatestSeveices();
}

$installer->endSetup();

