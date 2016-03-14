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
     * Parses csv file and transforms sku to right link format
     * @return array
     */
    public function parseCsv()
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
                    $links[] = $siteUrl . $slashAdded . '/' . $data[0] . '.xml';
                }

                fclose($handle);
                $fileName = basename($file);
                $parsed[$fileName] = $links;
                $parsedPath = $importFilePath . DS . 'parsed';
                $moveTo = $parsedPath . DS . time() . '-' . $fileName;
                if (!is_dir($parsedPath)) {
                    mkdir($parsedPath);
                }
                rename($file, $moveTo);
            }
        }
        return $parsed;
    }
}