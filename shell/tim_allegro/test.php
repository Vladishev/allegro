<?php
ini_set('display_errors', 1);
require '../../app/Mage.php';
//umask(0);
Mage::app();

$import = Mage::getModel('tim_allegro/sendReports');
$import->sendReport();
exit;