<?php
class   Orba_Allegro_Model_Resource_Auction extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    const MODE_IGNORED = 0;     
    const MODE_ACTIVE = 1;
    const MODE_BOTH = 2;
    
    protected function _construct() {
        $this->_init('orbaallegro/auction', 'auction_id');
    } 
    
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        if($object->getData('serialized_data')){
            $this->setSerializedData($object, $object->getData('serialized_data'));
        }
        $this->updateTransactionItemsSold($object);
        $this->updateItemsSold($object);
        return parent::_afterSave($object);
    }
	
	/**
	 * @param array $ids
	 * @return null | int
	 */
	public function disableRenew(array $ids) {
		if(!$ids){
			return;
		}
		
		$adapter = $this->_getWriteAdapter();
		
		return $adapter->update(
				$this->getMainTable(), 
				array("do_renew" => 0, "renew_items"=>null), 
				$adapter->quoteInto("auction_id IN (?)", $ids)
		);
	}
    
    public function addAuctionCountToProductCollection(
            Mage_Catalog_Model_Resource_Product_Collection $collection, $status=null) {
        
        $statusArr = array();
        
        if(is_array($status)){
            $statusArr = $statusArr;
        }elseif ($status===true) {
            $statusArr[] = Orba_Allegro_Model_Auction_Status::STATUS_PLACED;
        }elseif($status!==null){
            $statusArr[] = $status;
        }
        
        if($status && !is_array($status)){
            $status = array($status);
        }
        
        $baseSelect = $collection->getSelect();
        
        $select = $baseSelect->getAdapter()->select();
        $select->from(
                array("auction"=>$this->getMainTable()),
                array(new Zend_Db_Expr("COUNT(auction.auction_id)"))
        );
        $select->where("auction.product_id=e.entity_id");
        
        if(count($statusArr)){
            $select->where("auction.auction_status IN (?)", $statusArr);
        }
        $baseSelect->columns(array("auction_count"=>$select));
        $collection->setFlag("orbaallegro_auctions_joined", true);
    }
    
    /**
     * Set times
     * @param Mage_Core_Model_Abstract $object
     * @return type
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        // Times
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        return parent::_prepareDataForSave($object);
    }
    
    
    /**
     * @param Mage_Core_Model_Abstract $object
     * @return mixed
     */
    public function getSerializedData(Mage_Core_Model_Abstract $object) {
        
        $adapter = $this->_getReadAdapter();
        $stmt = $adapter->select();
        $stmt->
            from(
                array('s'=>$this->getTable('orbaallegro/auction_serialized')),
                array("serialized_data")
            )->
            where('s.auction_id=?', $object->getId());
        
        if(!is_null($result = $adapter->fetchOne($stmt))){
            $data = unserialize($result);
            $imageMapper = array();
            if(isset($data['fields'])){
                // Process Images data
                $service = $object->getService();
                $base = $this->_getBaseDir();
                foreach($data['fields'] as $key=>$field){
                    foreach($this->getImages($service) as $image){
                        if($field[Orba_Allegro_Model_Form_Parser_Out::KEY_FID]==$image){
                            $filename = $data['fields'][$key][Orba_Allegro_Model_Form_Parser_Out::KEY_FVALUE_IMAGE];
                            $path = $base . DS . $filename;
                            $content = "";
                            if(file_exists($path)){
                                $content = file_get_contents($path);
                            }
                            $data['fields'][$key][Orba_Allegro_Model_Form_Parser_Out::KEY_FVALUE_IMAGE] = $content;
                            $imageMapper[$image] = $filename;
                        }
                    }
                }
                
            }
            $object->setData("image_mapper", $imageMapper);
            return $data;
        }
        
        return null;
    }
    
    protected function _getRelativePath() {
        return Mage::helper("orbaallegro")->getAuctionFilesPath();
    }
    
    protected function _getBaseDir() {
        return Mage::getBaseDir('media');
    }


    public function setSerializedData(Mage_Core_Model_Abstract $object, $serializedData) {
        
        
        $imageMapper = array();
        
        if($object->getId()){
            $object->getSerializedData();
            $imageMapper = $object->getImageMapper();
        }
        
        $service = $object->getService();

        $relPath = $this->_getRelativePath();
        $base = $this->_getBaseDir();
        
        // Dir exists?
        if(!is_dir($base . DS . $relPath)){
            $_pathArray = array($base);
            foreach (explode(DS, $relPath) as $dir){
                $_pathArray[] = $dir;
                $_path = implode(DS, $_pathArray);
                if(!is_dir($_path)){
                    mkdir($_path);
                }
            }
        }
        
        foreach($serializedData['fields'] as $key=>$field){
            foreach($this->getImages($service) as $image){
                if($field[Orba_Allegro_Model_Form_Parser_Out::KEY_FID]==$image){
                    $base64image = $field[Orba_Allegro_Model_Form_Parser_Out::KEY_FVALUE_IMAGE];
                    // No data - just remove field and continue
                    if(empty($base64image)){
                        unset($serializedData['fields'][$key]);
                        // If file exists remove it
                        if(isset($imageMapper[$image]) && file_exists($base . DS . $relPath. DS . $imageMapper[$image])){
                            @unlink($base . DS . $relPath . DS . $imageMapper[$image]);
                        }
                        if(isset($imageMapper[$image]) && file_exists($base . DS . $relPath. DS . $imageMapper[$image] . ".jpg")){
                            @unlink($base . DS . $relPath . DS . $imageMapper[$image] . ".jpg");
                        }
                        continue;
                    }
                    
                    $write = true;
                    // If # file exists in previous serialization & not clone mode
                    if(!$object->getCloneMode() && isset($imageMapper[$image]) && file_exists($base . DS . $imageMapper[$image])){
                        $filename = $imageMapper[$image];
                        if(md5($base64image)==md5(file_get_contents($base . DS . $imageMapper[$image]))){
                            $write = false; // same haseshes - skip writing
                        }
                    }else{
                        // Generate unique name until file dont exists
                        while(($filename=md5(uniqid(microtime()))) && file_exists($base . DS . $relPath. DS . $filename));
                        $filename = $relPath. DS . $filename;
                    }
                    
                    if($write){
                        file_put_contents($base . DS . $filename, $base64image);
                        file_put_contents($base . DS . $filename.".jpg", base64_decode($base64image));
                    }
                    
                    // Replace base64 with file path to file with contents
                    $serializedData['fields'][$key][Orba_Allegro_Model_Form_Parser_Out::KEY_FVALUE_IMAGE] = $filename;;
                }
            }
        }

        
        $serializedData = serialize($serializedData);
        
        $adapter = $this->_getWriteAdapter();
        $table = $this->getTable('orbaallegro/auction_serialized');
        $stmt = $adapter->select();
        $stmt->
            from(
                array('s'=>$table),
                array("auction_id")
            )->
            where('s.auction_id=?', $object->getId());
        
        if($adapter->fetchOne($stmt)){
            $adapter->update(
                $table, 
                array(
                    "serialized_data"=>$serializedData
                ), 
                $adapter->quoteInto("auction_id=?", $object->getId())
            );
        }else{
            $adapter->insert($table, 
                array(
                    "serialized_data"=>$serializedData,
                    'auction_id'=>$object->getId()
                )
            );
        }
        
        return $this;
    }
    
    public function getImages($service) {
        return array(
            $service::ID_IMAGE_1,
            $service::ID_IMAGE_2,
            $service::ID_IMAGE_3,
            $service::ID_IMAGE_4,
            $service::ID_IMAGE_5,
            $service::ID_IMAGE_6,
            $service::ID_IMAGE_7,
            $service::ID_IMAGE_8
        );
    }
    
    
    public function updateItemsSold(Varien_Object $auction) {
        $itemsSold = $auction->getItemsSold();
        $realCount = $this->getItemsSold($auction);
        if($realCount!==null && $itemsSold!=$realCount){
            $this->_getWriteAdapter()->update(
                    $this->getMainTable(), 
                    array("items_sold"=>$realCount), 
                    $this->_getWriteAdapter()->quoteInto("auction_id=?", $auction->getId())
            );
            $auction->setItemsSold($realCount);
        }
    }
    
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @return int
     */
    public function getActiveItemsSold(Orba_Allegro_Model_Auction $auction) {
        return $this->_getItemsSold($auction, self::MODE_ACTIVE);
    }
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @return int
     */
    public function getIgnoredItemsSold(Orba_Allegro_Model_Auction $auction) {
        return $this->_getItemsSold($auction, self::MODE_IGNORED);
    }
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @return int
     */
    public function getItemsSold(Orba_Allegro_Model_Auction $auction) {
        return $this->_getItemsSold($auction);
    }
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @param type $mode
     * @return int
     */
    protected function _getItemsSold(Orba_Allegro_Model_Auction $auction, $mode=self::MODE_BOTH) {
        $cond = null;
        switch ($mode) {
            case self::MODE_ACTIVE:
                $cond = 0;
            break;
            case self::MODE_IGNORED:
                $cond = 1;
            break;
        }
        
        $stmt = $this->_getReadAdapter()->select();
        $stmt->from(
                array("bid"=>$this->getTable('orbaallegro/auction_bid')),
                array("item_count"=>new Zend_Db_Expr("SUM(bid.item_quantity)"))
        );
        
        if($mode!==null){
            $stmt->where("bid.is_ignored=?", $cond);
        }
        
        $stmt->where("bid.auction_id=?", $auction->getId());
        $stmt->where("bid.is_deleted=?", 0);
        // $stmt->where("bid.bid_status=?", Orba_Allegro_Model_Auction_Bid::STATUS_BID_SOLD);
        $stmt->group("bid.auction_id");
        
        $result = $this->_getReadAdapter()->fetchOne($stmt);
        
        return $result ? $result : 0;
    }
    
    /**
     * @param Varien_Object $auction
     */
    public function updateTransactionItemsSold(Varien_Object $auction) {
        $itemsSold = $auction->getTransactionItemsSold();
        $realCount = $this->getTransactionItemsSold($auction);
        if($realCount!==null && $itemsSold!=$realCount){
            $this->_getWriteAdapter()->update(
                    $this->getMainTable(), 
                    array("transaction_items_sold"=>$realCount), 
                    $this->_getWriteAdapter()->quoteInto("auction_id=?", $auction->getId())
            );
            $auction->setTransactionItemsSold($realCount);
        }
    }
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @return int
     */
    public function getActiveTransactionItemsSold(Orba_Allegro_Model_Auction $auction) {
        return $this->_getTransactionItemsSold($auction, self::MODE_ACTIVE);
    }
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @return int
     */
    public function getIgnoredTransactionItemsSold(Orba_Allegro_Model_Auction $auction) {
        return $this->_getTransactionItemsSold($auction, self::MODE_IGNORED);
    }
    
    /**
     * @param Orba_Allegro_Model_Auction $auction
     * @return int
     */
    public function getTransactionItemsSold(Orba_Allegro_Model_Auction $auction) {
        return $this->_getTransactionItemsSold($auction);
    }
    
    /**
     * 
     * @param Orba_Allegro_Model_Auction $auction
     * @param type $mode
     * @return int
     */
    protected function _getTransactionItemsSold(Orba_Allegro_Model_Auction $auction, $mode=self::MODE_BOTH) {
        $cond = null;
        switch ($mode) {
            case self::MODE_ACTIVE:
                $cond = 0;
            break;
            case self::MODE_IGNORED:
                $cond = 1;
            break;
        }
        
        $stmt = $this->_getReadAdapter()->select();
        $stmt->from(
                array("trans_item"=>$this->getTable('orbaallegro/transaction_auction')),
                array("item_count"=>new Zend_Db_Expr("SUM(trans_item.quantity)"))
        );
        
        if($mode!==null){
            $stmt->join(
                    array("transaction"=>$this->getTable("orbaallegro/transaction")),
                    "trans_item.transaction_id=transaction.transaction_id",
                    array()
                );
            $stmt->where("transaction.is_ignored=?", $cond);
        }
        
        $stmt->where("trans_item.auction_id=?", $auction->getId());
        $stmt->where("transaction.is_deleted=?", 0);
        $stmt->group("trans_item.auction_id");
        
        $result = $this->_getReadAdapter()->fetchOne($stmt);
        
        return $result ? $result : 0;
    }
    
    public function getOfferCount(Varien_Object $auction, $status=null) {
        $stmt = $this->_getReadAdapter()->select();
        $stmt->from(
                array("bid"=>$this->getTable('orbaallegro/auction_bid')),
                array("item_count"=>new Zend_Db_Expr("COUNT(bid.bid_id)"))
        );
        
        $stmt->where("bid.auction_id=?", $auction->getId());
        $stmt->where("bid.is_deleted=?", 0);
        if($status!==null){
            $stmt->where("bid.bid_status=?", $status);
        }
        $stmt->group("bid.auction_id");
        
        $result = $this->_getReadAdapter()->fetchOne($stmt);
        
        return $result ? $result : 0;
                
    }
}