<?php
/**
 * Model to import a price
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Model_Import extends Mage_Core_Model_Abstract
{
    /**
     * A configuration path to "Price Update Enabled" setting
     *
     * @var string
     */
    const XPATH_PRICE_UPDATE_ENABLED = 'catalog/price_update/enabled';

    /**
     * A full path to an import CSV file
     *
     * @var string
     */
    const IMPORT_FILE_FULL_PATH = 'http://atm-crmsklep01:1200/CennikB24.csv';

    /**
     * Max amount of rows processed at once
     *
     * @var int
     */
    const MAX_ROWS_AT_ONCE = 5000;

    /**
     * A CSV file delimiter
     *
     * @var string
     */
    const CSV_FILE_DELIMITER = ',';

    /**
     * A CSV file enclosure
     *
     * @var string
     */
    const CSV_FILE_ENCLOSURE = '"';

    /**
     * An SKU column index
     *
     * @var int
     */
    const COLUMN_SKU = 1;

    /**
     * A manufacturer price column index
     *
     * @var int
     */
    const COLUMN_PRICE_MANUFACTURER = 2;

    /**
     * A CRM price column index (will be used to calculate a regular price)
     *
     * @var int
     */
    const COLUMN_PRICE = 3;

    /**
     * An registered price column index
     *
     * @var int
     */
    const COLUMN_PRICE_REGISTERED = 4;

    /**
     * An array of tasks (for performance tests)
     *
     * @var array
     */
    protected $_tasks = array();

    /**
     * Current store id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Old product price
     *
     * @var float
     */
    protected $_oldProductPrice;

    /**
     * Runs prices import
     *
     * @param string $file    a CSV file to import prices from
     * @param bool   $convert defines if converting from UTF-16LE to UTF-8 encoding is needed (it's not needed in case if file is already in UTF-8)
     *
     * @return Tim_PriceUpdate_Model_Import
     */
    public function run($file = null, $convert = true)
    {
        if (!Mage::getStoreConfig(self::XPATH_PRICE_UPDATE_ENABLED)) {
            return $this;
        }
        $this->_start('run');

        $fileName = !is_null($file) ? $file : (self::IMPORT_FILE_FULL_PATH);
        if ($convert) {
            $fileName = $this->_prepareFile($fileName);
        }

        if ($fileName === false) {
            return $this;
        }

        if (!is_file($fileName) || substr($fileName, -3) != 'csv') {
            $this->_logError('Error. Unable to find a file, or file extension is not CSV: ' . $fileName);
            return $this;
        }

        $updatedTotal = 0;
        $fileHandle = fopen($fileName, 'r');
        if ($fileHandle) {
            $data = array();
            $count = 0;

            while ($row = $this->_readCsvRow($fileHandle)) {
                if (!$this->_validateRow($row)) {
                    continue;
                }

                $sku = $row[self::COLUMN_SKU];
                $data[$sku] = $row;

                if ($count++ > self::MAX_ROWS_AT_ONCE) {
                    $updatedTotal += $this->_importData($data);
                    $data = array();
                    $count = 0;
                }
            }

            if (!empty($data)) {
                $updatedTotal += $this->_importData($data);
            }

            fclose($fileHandle);

            $this->_start('reindex');
            /* @var $indexer Mage_Index_Model_Indexer */
            $indexer = Mage::getSingleton('index/indexer');
            $process = $indexer->getProcessByCode('catalog_product_price');
            $process->reindexEverything();
            $this->_finish('reindex');
        } else {
            $this->_logError("Error. Couldn't get a file.");
        }

        $this->_finish('run', 'updated total: ' . $updatedTotal);

        unlink($fileName);

        return $this;
    }

    /**
     * Converts a CSV file from UTF-16LE encoding to UTF-8, returns a new file name
     *
     * @param string $fileName UTF-16LE file name
     *
     * @return string | false
     */
    protected function _prepareFile($fileName)
    {
        $content = file_get_contents($fileName);

        if (!is_string($content)) {
            $this->_logError("Error. Couldn't get a file.");
            return false;
        }

        // remove UTF-16LE BOM from the start of a file
        $bom = chr(255).chr(254);
        $content = ltrim($content, $bom);

        // save into a new file
        $newFileName = substr($fileName, 0, -4) . '_UTF8.csv';
        $newFileName = Mage::getBaseDir('var') . DS . basename($newFileName);
        $result = file_put_contents($newFileName, mb_convert_encoding($content, 'UTF-8', 'UTF-16LE'));
        if (!$result) {
            $this->_logError('Error. Unable to create a CSV file.');
            return false;
        }

        return $newFileName;
    }

    /**
     * Validates a row and returns true if validation passed
     *
     * @param array $row row
     *
     * @return bool
     */
    protected function _validateRow($row)
    {
        $result = true;
        if (!isset($row[self::COLUMN_SKU]) || empty($row[self::COLUMN_SKU])) {
            $this->_logError('SKU is not set.');
            $result = false;
        } else {
            if (!isset($row[self::COLUMN_PRICE])) {
                $this->_logError('Price is not set.');
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Returns an array with data from one row of a CSV file
     *
     * @param resource $fileHandle file handle
     *
     * @return array | false
     */
    protected function _readCsvRow($fileHandle)
    {
        return fgetcsv($fileHandle, 0, self::CSV_FILE_DELIMITER, self::CSV_FILE_ENCLOSURE);
    }

    /**
     * Imports a price data, returns a count of updated products
     *
     * @param array $data price data array with SKU as a key
     *
     * @return int
     */
    protected function _importData($data)
    {
        $this->_start('importData');
        $this->_unsetNotExistingSku($data);
        if (empty($data)) {
            return 0;
        }

        // note: I have commented out unset of not changed prices, because they couldn't be calculated at this moment, so, couldn't be compared to know if it were changed
        // there is a possibility to get product data and calculate prices here, but not sure if it's necessary for now
        // todo: figure out if this code should be used (make sense in improving a performance) and remove it if not
        /*$this->_unsetNotChangedSku($data);
        if (empty($data)) {
            return 0;
        }*/

        $allSku = array_keys($data);
        /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getModel('catalog/product')->setStoreId(0)->getCollection();
        $productCollection->addFieldToFilter('sku', array('in' => $allSku));
        $productCollection->addAttributeToSelect(array('price', 'tim_jednostka_miary', 'tim_wolumen'));

        $updatedProducts = 0;
        foreach ($productCollection as $product) {
            /* @var $product Mage_Catalog_Model_Product */
            $sku = $product->getSku();
            $attributesData = array();
            // update a regular product price only if it is not set (0 or null)
            //if (!$product->getData('price')) {
                $data[$sku][self::COLUMN_PRICE] = $this->_convertPriceToValidFormat($data[$sku][self::COLUMN_PRICE]);
                $data[$sku][self::COLUMN_PRICE] = $this->_calculatePrice(
                    $data[$sku][self::COLUMN_PRICE],
                    $product->getData('tim_jednostka_miary'),
                    $product->getData('tim_wolumen')
                );
                if (!$this->_comparePrices($product->getData('price'), $data[$sku][self::COLUMN_PRICE])) {
                    $attributesData['price'] = $data[$sku][self::COLUMN_PRICE];
                }
            //}

            $data[$sku][self::COLUMN_PRICE_REGISTERED] = $this->_convertPriceToValidFormat($data[$sku][self::COLUMN_PRICE_REGISTERED]);
            $data[$sku][self::COLUMN_PRICE_REGISTERED] = $this->_calculatePrice(
                $data[$sku][self::COLUMN_PRICE_REGISTERED],
                $product->getData('tim_jednostka_miary'),
                $product->getData('tim_wolumen')
            );
//            if (!$this->_comparePrices($product->getData('tim_cena_ewidencyjna'), $data[$sku][self::COLUMN_PRICE_REGISTERED])) {
//                $attributesData['tim_cena_ewidencyjna'] = $data[$sku][self::COLUMN_PRICE_REGISTERED];
//            }

            $data[$sku][self::COLUMN_PRICE_MANUFACTURER] = $this->_convertPriceToValidFormat($data[$sku][self::COLUMN_PRICE_MANUFACTURER]);
            $data[$sku][self::COLUMN_PRICE_MANUFACTURER] = $this->_calculatePrice(
                $data[$sku][self::COLUMN_PRICE_MANUFACTURER],
                $product->getData('tim_jednostka_miary'),
                $product->getData('tim_wolumen')
            );
//            if (!$this->_comparePrices($product->getData('tim_cena_producenta'), $data[$sku][self::COLUMN_PRICE_MANUFACTURER])) {
//                $attributesData['tim_cena_producenta'] = $data[$sku][self::COLUMN_PRICE_MANUFACTURER];
//            }

            if (!empty($attributesData)) {
                if (!$this->_storeId) {
                    $this->_storeId = $product->getStoreId();
                }
                $this->_oldProductPrice = (float) $product->getData('price');
                $attributes = array();
                $attributes = array_keys($attributesData);
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                foreach ($attributes as $attribute) {
                    $product->setStoreId($this->_storeId)->setData($attribute, false);
                }
                $product->save();

                $this->_updateReport($product, $attributesData);
                foreach ($attributesData as $code => $value) {
                    $product->setStoreId($this->_storeId);
                    $product->setData($code, $value);
                    $product->getResource()->saveAttribute($product, $code);
                }
                Mage::app()->setCurrentStore($this->_storeId);

                $updatedProducts++;
            }
        }
        $this->_finish('importData', 'Updated products: ' . $updatedProducts);

        return $updatedProducts;
    }

    /**
     * Removes rows with SKU which are not exist in Magento
     *
     * @param array &$data data
     *
     * @return null
     */
    protected function _unsetNotExistingSku(&$data)
    {
        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $readAdapter = $resource->getConnection('read');
        $select = $readAdapter->select()
            ->from($resource->getTableName('catalog/product'), 'sku')
            ->where('sku IN (?)', array_keys($data));
        $result = $readAdapter->fetchAssoc($select);
        $data = array_intersect_key($data, $result);
    }

    /**
     * Removes rows which were not changed since last update (check this in a report)
     *
     * @param array &$data data
     *
     * @return null
     */
    protected function _unsetNotChangedSku(&$data)
    {
        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $readAdapter = $resource->getConnection('read');
        $select = $readAdapter->select()
            ->from(
                array('main_table' => $resource->getTableName('tim_priceupdate/report')),
                array('price_new', 'registered_price_new', 'manufacturer_price_new')
            )
            ->joinLeft(
                array('product_table' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
                "main_table.product_id = product_table.entity_id",
                array('sku')
            )
            ->where('product_table.sku IN (?)', array_keys($data));
        $result = $readAdapter->fetchAll($select);
        foreach ($result as $priceData) {
            $sku = $priceData['sku'];
            // if all prices were not changed since last import - unset this product from data
            if ($priceData['price_new'] // regular price should be updated only if it is 0 or NULL
                && $this->_comparePrices($priceData['registered_price_new'], $this->_convertPriceToValidFormat($data[$sku][self::COLUMN_PRICE_REGISTERED]))
                && $this->_comparePrices($priceData['manufacturer_price_new'], $this->_convertPriceToValidFormat($data[$sku][self::COLUMN_PRICE_MANUFACTURER]))
            ) {
                unset($data[$sku]);
            }
        }
    }

    /**
     * Updates a report with a new data
     *
     * @param Mage_Catalog_Model_Product $product        product
     * @param array                      $attributesData updated attributes
     *
     * @return null
     */
    protected function _updateReport($product, $attributesData)
    {
        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $writeAdapter = $resource->getConnection('write');

        $attributesData['price'] = isset($attributesData['price']) ? $attributesData['price'] : $product->getData('price');
        $attributesData['tim_cena_ewidencyjna'] = isset($attributesData['tim_cena_ewidencyjna']) ? $attributesData['tim_cena_ewidencyjna'] : $product->getData('tim_cena_ewidencyjna');
        $attributesData['tim_cena_producenta'] = isset($attributesData['tim_cena_producenta']) ? $attributesData['tim_cena_producenta'] : $product->getData('tim_cena_producenta');

        $data = array(
            'product_id' => $product->getId(),
            'price_old' => $this->_oldProductPrice,
            'price_new' => (float) $attributesData['price'],
            'price_diff' => $this->_calculatePriceDiffPercentage($this->_oldProductPrice, $attributesData['price']),
//            'registered_price_old' => (float) $product->getData('tim_cena_ewidencyjna'),
//            'registered_price_new' => (float) $attributesData['tim_cena_ewidencyjna'],
//            'registered_price_diff' => $this->_calculatePriceDiffPercentage($product->getData('tim_cena_ewidencyjna'), $attributesData['tim_cena_ewidencyjna']),
//            'manufacturer_price_old' => (float) $product->getData('tim_cena_producenta'),
//            'manufacturer_price_new' => (float) $attributesData['tim_cena_producenta'],
//            'manufacturer_price_diff' => $this->_calculatePriceDiffPercentage($product->getData('tim_cena_producenta'), $attributesData['tim_cena_producenta']),
            'updated_at' => Mage::getSingleton('core/date')->gmtDate(),
        );
        $writeAdapter->insertOnDuplicate($resource->getTableName('tim_priceupdate/report'), $data);
    }

    /**
     * Returns a price difference in percentage
     *
     * @param string $priceOne price one
     * @param string $priceTwo price two
     *
     * @return float
     */
    protected function _calculatePriceDiffPercentage($priceOne, $priceTwo)
    {
        if ($this->_comparePrices($priceOne, $priceTwo)) {
            $priceDiff = 0;
        } else {
            $priceDiff = (($priceTwo / $priceOne) - 1) * 100;
        }

        return (float) $priceDiff;
    }

    /**
     * Converts price to a valid format and returns it
     *
     * @param string $price a price
     *
     * @return string
     */
    protected function _convertPriceToValidFormat($price)
    {
        $price = str_replace(',', '.', $price);
        $price = round($price, 2);

        return $price;
    }

    /**
     * Compares two prices. Returns true if they are equal.
     *
     * @param string $priceOne price one
     * @param string $priceTwo price two
     *
     * @return bool
     */
    protected function _comparePrices($priceOne, $priceTwo)
    {
        $priceOne = round($priceOne, 2);
        $priceOne = explode('.', $priceOne);
        $priceOneInteger = ltrim($priceOne[0], '0');
        $priceOneFractional = isset($priceOne[1]) ? rtrim($priceOne[1], '0') : '';
        $priceOneFractional = substr($priceOneFractional, 0, 4); // fractional part could be maximum 4 digits (as it stored in database)

        $priceTwo = round($priceTwo, 2);
        $priceTwo = explode('.', $priceTwo);
        $priceTwoInteger = ltrim($priceTwo[0], '0');
        $priceTwoFractional = isset($priceTwo[1]) ? rtrim($priceTwo[1], '0') : '';
        $priceTwoFractional = substr($priceTwoFractional, 0, 4); // fractional part could be maximum 4 digits (as it stored in database)

        return ($priceOneInteger == $priceTwoInteger) && ($priceOneFractional == $priceTwoFractional);
    }

    /**
     * Calculates a price according to a formula
     *
     * @param string $inputPrice      price
     * @param string $measurementUnit measurement unit
     * @param string $volume          volume
     *
     * @return float
     */
    protected function _calculatePrice($inputPrice, $measurementUnit, $volume)
    {
        switch ($measurementUnit) {
            case 'km':
                $price = ($inputPrice / 1000) * $volume;
                break;
            case 'm':
                $price = $inputPrice * $volume;
                break;
            default:
                $price = $inputPrice * $volume;
        }

        return round($price, 2);
    }

    /**
     * Logs an error
     *
     * @param string $message an error message
     *
     * @return null
     */
    protected function _logError($message)
    {
        if ($this->_isLoggingEnabled()) {
            Mage::log($message, null, 'price_update.log');
        }
    }

    /**
     * Remembers a task start time (for performance tests)
     *
     * @param string $task a unique key to identify a task
     *
     * @return null
     */
    protected function _start($task)
    {
        $this->_tasks[$task] = microtime(true);
    }

    /**
     * Finishes a task and inserts it's time to a log (for performance tests)
     *
     * @param string $task           a unique key to identify a task
     * @param string $additionalInfo any text message (or an array of messages) to add to a log
     *
     * @return null
     */
    protected function _finish($task, $additionalInfo = '')
    {
        if (isset($this->_tasks[$task])) {
            if (is_array($additionalInfo)) {
                $additionalInfo = implode(' | ', $additionalInfo);
            }

            $finish = microtime(true);
            $time = $finish - $this->_tasks[$task];

            if ($this->_isLoggingEnabled()) {
                Mage::log("Task '$task': $time seconds. $additionalInfo", null, 'reports_performance.log');
            }

            unset($this->_tasks[$task]);
        }
    }

    /**
     * Returns logging enabled or not
     *
     * @return bool
     */
    protected function _isLoggingEnabled()
    {
        return Mage::getStoreConfig('catalog/price_update/logging_enabled');
    }
}