<?php

/**
 * Class Tim_UpdateQuantity_Model_Wms
 */
class Tim_UpdateQuantity_Model_Wms
{
    /**
     * External products array
     *
     * @var array
     */
    protected $_wmsCollection;
    /**
     * Internal products array
     *
     * @var array
     */
	protected $_products;
    /**
     * Internal products sku array
     *
     * @var array
     */
	protected $_productSkus;

    /**
     * @param array $products
     * @return bool
     */
	public function setProductCollection($products)
	{
		if(is_array($products)){
			$this->_products = $products;
			$skus = array();
			foreach($products as $prod){
				$skus[] = '\''.$prod['sku'].'\'';
			}
			$this->_productSkus = $skus;
			return true;
		}
		return false;
	}

    /**
     * Returns array with product which needed to update
     *
     * @return array
     */
	public function getUpdatedData()
	{
		$timestampUpdate = microtime(true);
		$productCount = count($this->_products);

		foreach($this->_products as $key => $prod){
			$qty = 0; 
			$sections = array();
			$sections_outlet = array();
			foreach($this->findInWms($prod['sku']) as $wms){
				$wms_qty  = $wms['stkr_freequantity'];
				if(strtolower(trim($wms['stkr_SUunitCode'])) == 'km'){
					$wms_qty = $wms_qty * 1000;
				}
				if($wms_qty == 0){
					continue;
				}
                if($prod['wolumen'] && (int)$prod['wolumen'] != 0) {
                    $wms_qty = $wms_qty / (int)$prod['wolumen'];
                }
				
				$qty += $wms_qty;
				
				if($prod['type'] == 'barrel'){
					if($prod['max_length'] < $wms_qty){
						$sections[] = $wms_qty;
					} else {
						$sections_outlet[] = $wms_qty; 
					}
				}
			}
			
			sort($sections);
			sort($sections_outlet);
			
			$this->_products[$key]['qty'] = $qty;
			$this->_products[$key]['sections'] = (string)implode(',',$sections);
			$this->_products[$key]['sections_outlet'] = (string)implode(',',$sections_outlet);
			
			global $timestamp;
			echo "\r Przeliczanie: ".( (int)($key/$productCount*100) )."%. czas: ". round(microtime(true) - $timestampUpdate,2)."s\033[?25l";
		}
		
		echo "\r Przeliczono w czasie: ". round(microtime(true) - $timestampUpdate,2)."s (". round(microtime(true) - $timestamp,2) ."s)\033[?25l \n";
		return $this->_products;
			
	}

    /**
     * Looks for a matches
     *
     * @param array $sku
     * @return array
     */
	protected function findInWms($sku)
	{
		$wmsRows = $this->getWmsCollection();
		$result = array();
		foreach($wmsRows as $row){
			if($sku == $row['stkr_prdPrimaryCode']){
				$result[] = $row;
			}
		}
		
		return $result;
	}

    /**
     * @return array|bool
     */
    protected function getWmsCollection()
    {
		$timestampWms = microtime(true);
		if($this->_wmsCollection){
			return $this->_wmsCollection;
		}
		if(!$this->_productSkus){
			return false;
		}
		
		$general = Mage::getModel('tim_update_quantity/general');
		$db = $general->getWmsAccess();

		$dbhandle = mssql_connect($db['wms_host'].':'.$db['wms_port'],$db['wms_user'],$db['wms_pwd']);
        $selected = mssql_select_db($db['wms_db'], $dbhandle);
		if(!$selected){
			$general->mail('Błąd Mssql!','Błąd połączenia z WMS','Couldn\'t open database '.$db['wms_db'].' ('.$db['wms_host'].')');
			die('Couldn\'t open database '.$db['wms_db'].' ('.$db['wms_host'].')');
		}

        $query = "SELECT [stkr_prdPrimaryCode],[stkr_freequantity],[stkr_SUunitCode]";
        $query .= " FROM [LvisionReport].[dbo].[LV_StockReport] WHERE stkr_prdPrimaryCode IN (".(implode(',',$this->_productSkus)).") AND stkr_loscode='01'";
		
        $result = mssql_query($query);
		
		if(!$result){
			printf("\n Błąd: %s",mssql_get_last_message());
			$general = new General();
			$general->mail('Błąd Mssql!','Błąd pobierania danych z WMS',mssql_get_last_message());
			die;
		}

        $rows = array();
		global $timestamp;
		
        while($row = mssql_fetch_array($result,MSSQL_ASSOC))
        {
			echo "\r Pobieranie danych z WMS ... czas: ". round(microtime(true) - $timestamp,2)."\033[?25l";
			$rows[] = $row;
        }
        mssql_close($dbhandle);
		
		echo "\r Pobrano dane z WMS w czasie: ". round(microtime(true) - $timestampWms,2)."\033[?25ls                \n";

        return $this->_wmsCollection = $rows;
    }
}
