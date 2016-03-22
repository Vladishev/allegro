<?php
ini_set('display_errors', 1);
require '../../app/Mage.php';
Mage::app();

$import = Mage::getModel('tim_allegro/importProducts');
$import->run();
exit;