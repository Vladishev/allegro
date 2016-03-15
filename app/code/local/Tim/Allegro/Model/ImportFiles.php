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
        $csvData = $this->_parseCsv();

        $xmlImportPath = Mage::getBaseDir('var') . DS . 'tim_import' . DS . 'xml';
        if (!is_dir($xmlImportPath)) {
            mkdir($xmlImportPath);
        }

        foreach ($csvData as $fileName => $links) {
            foreach ($links as $sku => $link) {
                $xml = file_get_contents($link);
                file_put_contents($xmlImportPath . DS . $sku . '.xml', $xml);
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
        foreach($files as $file) {
            $links = array();
            if (($handle = fopen($file, "r"))  !== false) {
                while (($data = fgetcsv($handle, 200)) !== FALSE) {
                    $replaced = str_replace('-', '/', $data[0]);
                    $slashAdded = substr_replace($replaced, '/', 8, 0);
                    $slashAdded = substr_replace($slashAdded, '/', 15, 0);
                    $links[$data[0]] = $siteUrl . $slashAdded . '/' . $data[0] . '.xml';
                }

                fclose($handle);
                $parsed[basename($file)] = $links;
                unlink($file);
            }
        }
        return $parsed;
    }
}