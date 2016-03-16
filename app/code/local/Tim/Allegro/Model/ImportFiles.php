<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_ImportFiles extends Mage_Core_Model_Abstract
{
    /**
     * Runs import of xml files
     */
    public function run()
    {
        $csvPath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'csv';
        $csvData = $this->_parseCsv();

        $xmlImportPath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'xml';
        if (!is_dir($xmlImportPath)) {
            mkdir($xmlImportPath);
        }

        $importModel = Mage::getModel('tim_allegro/import');

        foreach ($csvData as $fileName => $links) {
            $brokenLink = '';
            $file = $importModel->load($fileName, 'file_name');
            $lastSku = $file->getLastSku();

            //Cut array till not downloaded link
            if (!empty($lastSku)) {
                $i = 0;
                foreach ($links as $sku => $link) {
                    if ($sku == $lastSku) {
                        break;
                    }
                    $i++;
                }
                if ($i > 0) {
                    $links = array_slice($links, $i);
                }
            }
            foreach ($links as $sku => $link) {
                $xml = file_get_contents($link);

                //If can't download file - break foreach loop
                if ($xml === false) {
                    $brokenLink = $sku;
                    $file->setExecutedDate(date('Y-m-d H:i:s'))
                        ->setStatus(0)
                        ->setLastSku($brokenLink);
                    break;
                }

                file_put_contents($xmlImportPath . DS . $sku . '.xml', $xml, FILE_APPEND);
            }
            //If all links downloaded set data and delete csv file
            if (empty($brokenLink)) {
                $file->setExecutedDate(date('Y-m-d H:i:s'))
                    ->setStatus(1)
                    ->setLastSku($brokenLink);
                unlink($csvPath . DS . $fileName);
            }
            try {
                $importModel->save();
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'tim_import.log');
            }
        }
    }

    /**
     * Parses csv file and transforms sku to right link format
     * @return array
     */
    protected function _parseCsv()
    {
        $siteUrl = 'http://crmmedia.tim.pl/Products/PIM-XML/';
        $parsed = array();
        $importFilePath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'csv';
        $files = glob($importFilePath . DS . '*.csv');
        foreach ($files as $file) {
            $links = array();
            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 200)) !== FALSE) {
                    $replaced = str_replace('-', '/', $data[0]);
                    $slashAdded = substr_replace($replaced, '/', 8, 0);
                    $slashAdded = substr_replace($slashAdded, '/', 15, 0);
                    $links[$data[0]] = $siteUrl . $slashAdded . '/' . $data[0] . '.xml';
                }

                fclose($handle);
                $parsed[basename($file)] = $links;

                $issetFile = Mage::getModel('tim_allegro/import')
                    ->load(basename($file), 'file_name')
                    ->getData();
                //If file not exist in database - add it
                if (empty($issetFile)) {
                    $importModel = Mage::getModel('tim_allegro/import')
                        ->setFileName(basename($file))
                        ->setCreatedDate(date('Y-m-d H:i:s'))
                        ->setStatus(0);
                    try {
                        $importModel->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_import.log');
                    }
                }
            }
        }
        return $parsed;
    }
}