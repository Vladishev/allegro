<?php

/* Get tamplate skin from $_SERVER */
$ORBAALLEGRO_EXTENDED_TEMPLATE_DIRECTORY = "extended";

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

/******************************************************************************
 *
 * Install Default Template
 * 
 ******************************************************************************/
$baseDir = __DIR__ . DS . "contents" . DS . $ORBAALLEGRO_EXTENDED_TEMPLATE_DIRECTORY;
$templateDir = $baseDir . DS . "template";
$codesToIds = Mage::getSingleton("orbaallegro/service")->getConstCodesToIds();
$codesToLabels = Mage::getSingleton("orbaallegro/service")->getConstCountryCodesToLabel();


foreach(glob($templateDir . DS . "*") as $file){
    $basename = basename($file, ".phtml");
    if(array_key_exists($basename, $codesToIds)){
        $contents = file_get_contents($file);
        $countryCode = $codesToIds[$basename];
        $label = $codesToLabels[$countryCode];
        $model = Mage::getModel("orbaallegro/template");
        /* @var $model Orba_Allegro_Model_Template */
        $model->setName(Mage::helper("orbaallegro")->__("Extended") . " " . $label);
        $model->setCountryCode($countryCode);
        $model->setTitle("{{var product.name}}");
        $model->setDescription($contents);
        $model->save();
    }
    
}