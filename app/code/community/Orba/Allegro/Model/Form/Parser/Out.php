<?php
/**
 * Convert form data to Allegro values using specified service
 */
class Orba_Allegro_Model_Form_Parser_Out extends Orba_Allegro_Model_Form_Parser_Abstract {
    
    const KEY_PREVIEW_ONLY = "previewOnly";
    const KEY_FIELDS_TO_MODIFY = "fieldsToModify";
    const KEY_FIELDS_TO_REMOVE = "fieldsToRemove";
    const KEY_ITEM_ID = "itemId";
            
    
    const KEY_LOCAL_ID = "localId";
    const KEY_FIELDS = "fields";
    
    const KEY_FID = "fid";
    const KEY_FVALUE_STRING = "fvalueString";
    const KEY_FVALUE_INT = "fvalueInt";
    const KEY_FVALUE_FLOAT = "fvalueFloat";
    const KEY_FVALUE_IMAGE = "fvalueImage";
    const KEY_FVALUE_DATETIME = "fvalueDatetime";
    const KEY_FVALUE_DATE = "fvalueDate";
    
    const ID_FVALUE_STRING = 1;
    const ID_FVALUE_INT = 2;
    const ID_FVALUE_FLOAT = 3;
    const ID_FVALUE_IMAGE = 7;
    const ID_FVALUE_DATETIME = 9;
    const ID_FVALUE_DATE = 13;
    
    const ID_DEFAULT_OPT = 0;
    
    /**
     * @var Orba_Allegro_Model_Service_Abstract
     */
    protected $_service;
    
    protected $res = array();
    protected $opt = array();
    
    protected $_typeMap = array(
        self::ID_FVALUE_STRING => self::KEY_FVALUE_STRING,
        self::ID_FVALUE_INT => self::KEY_FVALUE_INT,
        self::ID_FVALUE_FLOAT => self::KEY_FVALUE_FLOAT,
        self::ID_FVALUE_IMAGE => self::KEY_FVALUE_IMAGE,
        self::ID_FVALUE_DATETIME => self::KEY_FVALUE_DATETIME,
        self::ID_FVALUE_DATE => self::KEY_FVALUE_DATE,
    );


    public function __construct(Orba_Allegro_Model_Service_Abstract $service) {
        parent::__construct();
        $this->_service = $service;
    }
    
    public function extractValue($item, $field) {
        if(null!==($type=$this->extractType($item))){
            return $item[$type];
        }
        return null;
    }
    
    public function extractType($item) {
        foreach($this->_typeMap as $typeKey){
            if(array_key_exists($typeKey, $item)){
                return $typeKey;
            }
        }
        return null;
    }
    
    /**
     * Compare edit-mode data, return merged data by reference
     * @param array $data
     * @param Orba_Allegro_Model_Auction $auction
     * @param array $mergedData
     * @return array
     */
    public function parseAndCompare($data, Orba_Allegro_Model_Auction $auction, &$mergedData) {
        $data = $this->parse($data);
        $mergedData = array();
        $data = $data[self::KEY_FIELDS];
        $oldData = $auction->getSerializedData();
        $oldData = $oldData[self::KEY_FIELDS];
        $modified = array();
        $ignoredFields = $auction->getService()->getIgnoredInEdit();
        
        // Fill merged values with old data
        foreach ($oldData as $field){
            $mergedData[$field[self::KEY_FID]] = $field;
        }
        
        foreach($data as $field){
            // Skip ignored fields
            if(in_array($field[self::KEY_FID], $ignoredFields)){
                continue;
            }
            
            $found = false;
            foreach($oldData as $oldField){
                if($oldField[self::KEY_FID] == $field[self::KEY_FID]){
                    $found = true;
                    // Change value if found
                    if(count(array_diff($oldField, $field))){
                        $modified[] = $field;
                        $mergedData[$field[self::KEY_FID]] = $field;
                    }
                    break;
                }
            }
            // Append field if not found
            if(!$found){
                $modified[]=$field;
                $mergedData[$field[self::KEY_FID]] = $field;
            }
        }
        
        $toRemove = array();
        
        // Old fids are in new fids?
        foreach($oldData as $oldField){
            // Skip ignored fields
            if(in_array($oldField[self::KEY_FID], $ignoredFields)){
                continue;
            }
            $makeRemove = true;
            foreach($data as $field){
                 if($oldField[self::KEY_FID] == $field[self::KEY_FID]){
                     $makeRemove = false;
                     break;
                 }
            }
            if($makeRemove){
                $toRemove[] = $oldField[self::KEY_FID];
                if(isset($mergedData[$oldField[self::KEY_FID]])){
                    unset($mergedData[$oldField[self::KEY_FID]]);
                }
            }
        }
        
        $mergedData = array(self::KEY_FIELDS => array_values($mergedData));
        
        $auctionId = $auction->getAllegroAuctionId();
        
        $out = array(
            self::KEY_ITEM_ID => $auctionId + 0,
            self::KEY_FIELDS_TO_MODIFY => $modified,
            self::KEY_FIELDS_TO_REMOVE => $toRemove
        );
        
            
        return $out;
        
    }
    /**
     * @todo: Rename to method name
     * @param type $data
     * @param type $localId
     * @return type
     */
    public function parse($data, $localId = null) {
        $fields = array();
		$payLoad = array();
		
        if(isset($data['res'])){
            $this->_res = $data['res'];
            unset($data['res']);
        }
        if(isset($data['opt'])){
            $this->_opt = $data['opt'];
            unset($data['opt']);
        }
		
		$quantity = 0;
        foreach($data as $fid=>$item){
			if ($fid == Orba_Allegro_Model_Service_Allegropl::ID_QUANTITY) {
				$quantity = (int) $item;
			}			
			
			if (Mage::helper('orbaallegro')->getIsVariantsRequired($fid)) {
				$payLoad['variants'][0]['fid'] = (int) $fid;
				$payLoad['variants'][0]['quantities'][0]['mask'] = (int) array_shift($item);
				$payLoad['variants'][0]['quantities'][0]['quantity'] = $quantity;
				$resId = $this->_getResId($fid);
				$resType = $this->_getResType($resId);
				$field = array(
					self::KEY_FID => $fid,
					$resType => $payLoad['variants'][0]['quantities'][0]['mask']
				);
				$fields[] = $field;					
			} else {
				if(!is_null($field=$this->_parseField($item, $fid))
					&& $fid != Orba_Allegro_Helper_Data::ALLEGRO_VARIANTS) {
					$fields[] = $field;
				}
			}
        }
		
        foreach ($this->_checkForUploadFiles() as $uploadField){
            $found = false;
            foreach($fields as $key=>$currentField){
                if($uploadField[self::KEY_FID]==$currentField[self::KEY_FID]){
                    // Replace id FID found
                    $fields[$key] = $uploadField;
                    $found = true;
                    break;
                }
            }
            // Append if FID not found
            if(!$found){
                $fields[] = $uploadField;
            }
        }
        
		$this->_postProcessFields($fields);
		
        $payLoad[self::KEY_FIELDS] = $fields;
        if(!is_null($localId)){
            $payLoad[self::KEY_LOCAL_ID] = $localId;
        }
        
        return $payLoad;
    }
	
	/**
	 * Post process some fields - ex. disable auto renew auction
	 * @param array $fields
	 */
	public function _postProcessFields(array &$fields) {
		$service = $this->_service;
	
		foreach($fields as &$field){
			switch ($field['fid']){
				case $service::ID_AUTO_RENEW:
					$field[self::KEY_FVALUE_INT] = Orba_Allegro_Model_System_Config_Source_Attribute_Template_Autorenew::DO_NOT_RENEW;
				break;
			}
		}
	}
	
    /**
	 * @param array $item
	 * @param int $fid
	 * @return null|array
	 */
    protected function _parseField($item, $fid) {
        $resId = $this->_getResId($fid);
        $resType = $this->_getResType($resId);
        $optId = $this->_getOptId($resId);
        $value = $this->_prepareValue($item, $resId, $optId, $fid);
        if($value!==null){
            return array(
                self::KEY_FID => $fid,
                $resType => $value
            );
        }
        return null;
    }
    
    protected function _checkForUploadFiles() {
        $fields = array();
        if (isset($_FILES["auction"]) && isset($_FILES["auction"]["tmp_name"])
                && is_array($_FILES["auction"]["tmp_name"])) {

            $files = $_FILES["auction"]["tmp_name"];
            foreach ($files as $fid => $tmpName) {
                if (is_readable($tmpName) && is_file($tmpName)) {
                    // @todo check Mime type;
                    if ($data = $this->_readAndProcessFile($_FILES["auction"]["tmp_name"][$fid])) {
                        $fields[] = array(
                            self::KEY_FID => $fid,
                            self::KEY_FVALUE_IMAGE => $data
                        );
                    }
                }
            }
        }
        return $fields;
    }

    protected function _prepareValue($data, $resId, $optId, $fid) {
        if($data===""){
            return null; // Pole puste - pomijamy
        }
        switch ($resId) {
            case self::ID_FVALUE_INT;
                return $this->_processInt($data);
            break;
            case self::ID_FVALUE_FLOAT;
                return $this->_processFloat($data);
            break;
            case self::ID_FVALUE_IMAGE;
                return $this->_processFile($data, $fid);
            break;
            case self::ID_FVALUE_DATETIME:
                return $this->_processDatetime($data);
            break;
            case self::ID_FVALUE_DATE:
                return $this->_processDate($data);
            break;
        }
		
		if (is_array($data) && $fid == Orba_Allegro_Helper_Data::ALLEGRO_VARIANTS)  {
			$data = array_shift($data);
		}
		
        return $this->_processString($data);
    }
    
    protected function _processString($data) {
        return (string)$data;
    }

    protected function _processInt($data) {
        if(is_array($data)){
            return array_sum($data);
        }
        return (int)$data;
    }

    protected function _processFloat($data) {
        return floatval(str_replace(",", ".", $data));
    }
    
    protected function _processFile($data, $fid) {
        if(is_array($data)){
            // Don't use this one
            if(isset($data['skip']) && !empty($data['skip'])){
                return null;
            }  
            // Old value
            if(isset($data['value'])){
                return $this->_readAndProcessFile(Mage::getBaseDir('media') . "/" . $data['value']);
            }
        }
        return null;
    }
    
    protected function _processDatetime($data) {
        $date = new Zend_Date($data, $this->_getDateTimeFormat());
        return $date->getTimestamp();
    }
    
    protected function _processDate($data) {
        $date = new Zend_Date($data, $this->_getDateFormat());
        return $date->toString("dd-MM-yyyy");
    }


    protected function _getResType($resId) {
        if(isset($this->_typeMap[$resId])){
            return $this->_typeMap[$resId];
        }
        return self::KEY_FVALUE_STRING;
    }
    
    protected function _getResId($fid) {
        if(isset($this->_res[$fid])){
            return $this->_res[$fid];
        }
        return self::ID_FVALUE_STRING;;
    }
    
    protected function _getOptId($fid) {
        if(isset($this->_opt[$fid])){
            return $this->_opt[$fid];
        }
        return self::ID_DEFAULT_OPT;;
    }
    
 
    protected function _parseFile($fileInfo) {
        
    }

    protected function _bitMaskOr($value) {
        
    }
    
    protected function _bitMaskAnd($value) {
        
    }
    
    protected function _readAndProcessFile($filename) {
        try{
            $base = dirname($filename);
            $parts = explode(".", basename($filename));
            $newFilename = $base . DS . $parts[0]."-orbaallegro" . "." . (isset($parts[1]) ? $parts[1] : "jpg" );
            // Open file
            $processor = new Varien_Image($filename) ;
            // Resize It
            $processor->keepAspectRatio(1);
            $processor->resize(Mage::helper("orbaallegro")->getBlockWidth(), Mage::helper("orbaallegro")->getBlockHeight());
            $processor->save($newFilename);
            // Get contents 
            $contents = base64_encode(file_get_contents($newFilename));
            // Delete file
            @unlink($newFilename);
        }catch(Exception $e){
            return null;
        }
        return $contents;
    }
    

    
}
