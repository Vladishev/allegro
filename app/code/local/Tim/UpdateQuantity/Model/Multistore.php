<?php 

class Tim_UpdateQuantity_Model_Multistore
{
    const MAX_PRODUCTS_IN_PACKAGE = 5000;
    protected $_productCollection;
	
    public function getPackagesProductCollection()
    {
//        if($this->_productCollection){
//            return $this->_productCollection;
//        }
	
        $query = "select prod.entity_id as id, prod.sku as sku, stock.qty as qty, prod.type_id as type, attr.value as wolumen, attr2.value as max_length ";
        $query .= "from catalog_product_entity as prod ";
        $query .= "left join cataloginventory_stock_item as stock on prod.entity_id=stock.product_id ";
        $query .= "join eav_attribute as eav ";
        $query .= "join eav_attribute as eav2 ";
        $query .= "left outer join catalog_product_entity_text as attr on prod.entity_id=attr.entity_id and attr.attribute_id=eav.attribute_id ";
        $query .= "left outer join catalog_product_entity_varchar as attr2 on prod.entity_id=attr2.entity_id and attr2.attribute_id=eav2.attribute_id ";
//        $query .= "where eav.attribute_code='tim_wolumen' and eav2.attribute_code='tim_odcinki_wyprz_max_dl' and prod.type_id <> 'virtual' ";
        $query .= "where eav.attribute_code='tim_wolumen' and eav2.attribute_code='tim_crm_id' and prod.type_id <> 'virtual' ";
        $query .= "order by prod.entity_id ";

        return  $this->_productCollection = $this->mysql($query);

    }

    private function mysql($query,$is_update = false)
    {
        $timestampUpdate = microtime(true);
        global $timestamp;
        if($is_update === true){
//			$file = '/var/www/multistore/dev/scripts/Tim/stock_update/query.txt';
//			$current = file_get_contents($file);;
//			file_put_contents($file, $query);
                echo "\r Aktualizacja Multistore'a.\033[?25l";
        }
		
        $general = Mage::getModel('tim_update_quantity/general');
        $db = $general->getMultistoreAccess();

        $con = mysqli_connect($db['host'],$db['user'],$db['pass']);
        if(mysqli_connect_errno()){
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
//			$general = new General();
			$general->mail('Błąd Mysql!','Błąd połączenia z bazą Multistore',"Failed to connect to MySQL: " . mysqli_connect_error());
			die;
        }

        mysqli_select_db ( $con,$db['dbname'] );

        $result = mysqli_query($con,$query);

        if($is_update === true){
                if($result){
                        echo "\r Zaktualizowano w czasie: ". round(microtime(true) - $timestampUpdate,2)."s (". round(microtime(true) - $timestamp,2) ."s)\033[?25l                           \n";
                } else {
                        printf("\n Błąd: %s",mysqli_error($con));
//                        $general = new General();
                        $general->mail('Błąd Mysql!','Błąd aktualizacji stanów magazynowych',mysqli_error($con));
                }
                mysqli_close($con);
                return $result;
        }
        $packNumber = 0;
        $rowNumber = 0;
        $rows = array();
        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
            if($rowNumber >= self::MAX_PRODUCTS_IN_PACKAGE){
                $rowNumber = 0;
                $packNumber++;
            }
//                echo "\r Pobieranie danych z Multistore ... czas: ". round(microtime(true) - $timestampUpdate,2)."s\033[?25l";
            $rows[$packNumber][] = $row;
            
            $rowNumber++;
        }
	
        mysqli_close($con);
        echo "\r Pobrano dane z Multistore w czasie: ". round(microtime(true) - $timestampUpdate,2)."s (". round(microtime(true) - $timestamp,2) ."s)\033[?25l \n";
        return $rows;

    }
	
	public function update($products)
	{
		if(!(is_array($products))){
			return false;
		}
		
		$queryInsert = array();
		$query_qty = '';
		$query_is_in_stock = '';
		$query_sections = '';
		$where = '';
		$productIds = array();
		/*$skus = array(
'1123-112AE-YY010',
'1123-112AF-YY012',
'1128-422CD-YY002',
'1123-112AG-YY004',
'1123-132AA-LC031',
'1126-121EI-YY004',
);*/
		foreach($products as $prod){
			if($prod['type'] == 'barrel'){
				$queryInsert[] = "(0,4,{$prod['id']},(select attribute_id from eav_attribute where attribute_code='tim_odcinki_wyprz'),\"".($prod['sections_outlet'] ? $prod['sections_outlet'] : "")."\")";
				$queryInsert[] = "(0,4,{$prod['id']},(select attribute_id from eav_attribute where attribute_code='tim_odcinki'),\"".($prod['sections'] ? $prod['sections'] : "")."\")";
			}
			$query_qty .= sprintf("when %d then %d \n",$prod['id'],$prod['qty']);
			$query_is_in_stock .= sprintf("when %d then %d \n",$prod['id'], $prod['qty'] != 0 ? 1 : 0 );
			$query_sections .= sprintf("when attr.entity_id = %d and attr.attribute_id=(%s) then \"%s\" \n",$prod['id'],"select eav.attribute_id from eav_attribute as eav where eav.attribute_code='tim_odcinki'",($prod['sections'] ? $prod['sections'] : ""));
			$query_sections .= sprintf("when attr.entity_id = %d and attr.attribute_id=(%s) then \"%s\" \n",$prod['id'],"select eav.attribute_id from eav_attribute as eav where eav.attribute_code='tim_odcinki_wyprz'",($prod['sections_outlet'] ? $prod['sections_outlet'] : ""));
			
			//$where .= sprintf("(stock.product_id = %d) or ",$prod['id']);
			$where .= sprintf("((attr.entity_id = %d and attr.attribute_id = (%s)) or ",$prod['id'],"select eav.attribute_id from eav_attribute as eav where eav.attribute_code='tim_odcinki'");
			$where .= sprintf("(attr.entity_id = %d and attr.attribute_id = (%s))) or ",$prod['id'],"select eav.attribute_id from eav_attribute as eav where eav.attribute_code='tim_odcinki_wyprz'");
			
			
			$productIds[] = $prod['id'];

		}
		
		//$where = "where $where ";
		
		//$where = rtrim($where,"or ");
		
		//$where = "where stock.product_id in (".(implode(',',$productIds)).") or ((attr.entity_id in (".(implode(',',$productIds)).") and attr.attribute_id in ((select attribute_id from eav_attribute as eav where eav.attribute_code='tim_odcinki'),(select attribute_id from eav_attribute where attribute_code='tim_odcinki_wyprz'))))";
		
		if(count($queryInsert) != 0 ){
			$query_insert = "insert ignore into catalog_product_entity_text (store_id,entity_type_id,entity_id,attribute_id,value) values ".(implode(',',$queryInsert))."; \n";
			if(!$this->mysql($query_insert,true)){
				die('err');
			}
		}
		
		$query = "update cataloginventory_stock_item as stock\n";
		
		$query .= "set stock.qty = case stock.product_id \n";
		$query .= $query_qty."\n";
		$query .= "end, \n";
		
		$query .= "stock.is_in_stock = case stock.product_id \n";
		$query .= $query_is_in_stock."\n";
		$query .= "end \n";
		
		$query .= "where stock.product_id in (".(implode(',',$productIds)).")\n";
		$this->mysql($query,true);
		
		$query = "update cataloginventory_stock_item as stock\n";
		$query .= "left join catalog_product_entity_text as attr on stock.product_id = attr.entity_id \n";
		
		$query .= "set attr.value = case \n";
		$query .= $query_sections."\n";
		$query .= "end \n";
			
		$query .= rtrim("where $where ","or ");
		
		return $this->mysql($query,true);
		
	}

}
