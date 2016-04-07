<?php

class Orba_Allegro_Model_Form_Parser_In extends Orba_Allegro_Model_Form_Parser_Abstract {
   
    /**
     * Mapuje typy specjlane
     * @var array
     */
    protected $_typeMap = array(
        'orbaallegro_category' => array(Orba_Allegro_Model_Form_Auction::FIELD_CATEGORY),
        'orbaallegro_shop_category' => array(Orba_Allegro_Model_Form_Auction::FIELD_SHOP_CATEGORY),
        'orbaallegro_country' => array(Orba_Allegro_Model_Form_Auction::FIELD_COUNTRY),
        'orbaallegro_editor'  =>array(Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION)
    );
    
    /**
     * Mapuje specjlane konfiguracje dla danych elemntow
     * @var array
     */
    protected $_configMap = array();
    
    public function __construct($attrs) {
        $this->setForm($attrs[0]);
        
        $this->_configMap = array();
        
        $this->_configMap[Orba_Allegro_Model_Form_Auction::FIELD_CATEGORY] = 
                array('readonly'=>true, 'note'=>'');
        
        $this->_configMap[Orba_Allegro_Model_Form_Auction::FIELD_DESCRIPTION] = 
                array('style'=>'width: 700px');
        
        $revMap = $this->_getRevMap();
        $stateFiled = isset($revMap[Orba_Allegro_Model_Form_Auction::FIELD_PROVINCE]) ?
            $revMap[Orba_Allegro_Model_Form_Auction::FIELD_PROVINCE] : null;
        
        if($stateFiled){
            $this->_configMap[Orba_Allegro_Model_Form_Auction::FIELD_COUNTRY ] = 
                array('state_field_id'=>$stateFiled);
        }
        
        // Prepare readonly fields
        if($this->getForm()->getEditMode()){
            $ignored = $this->getForm()->getService()->getIgnoredInEdit();
            $map = $this->_getMap();
            foreach($ignored as $ignoredFieldId){
                if(isset($map[$ignoredFieldId])){
                    $filedName = $map[$ignoredFieldId];
                    if(isset($this->_configMap[$filedName])){
                        $this->_configMap[$filedName] = array_merge($this->_configMap[$filedName], array(
                            "readonly"=>true
                        ));
                    }else{
                        $this->_configMap[$filedName] = array(
                            "readonly"=>true
                        );
                    }
                }
            }
        }
        
    }
    
    /**
     * @param mixed $value
     * @param int $allegroTypeId
     * @return mixed
     */
    public function parseAuctionValue($value, $allegroFid, $allegroTypeId, Orba_Allegro_Model_Auction $auction) {
        switch ($allegroTypeId) {
            case self::VALUE_TYPE_INPUT_CHECKBOX:
                return $this->_bitMaskToValues($value);
            break;
            case self::VALUE_TYPE_INPUT_FLOAT:
                return (float)$value;
            break;
            case self::VALUE_TYPE_INPUT_NUMBER:
                return (int)$value;
            break;
            case self::VALUE_TYPE_INPUT_FILE:
                // Need to decode base64 and return binary file path
                return $this->_processBase64Image($value, $allegroFid, $auction);
            break;
        }
        
        // Text values 0 is wrong for text values
        return $value;
    }
    
    public function parseFieldConfig($item){
        
        $config = array();
        
        $spacialConfig=$this->_getSpecialConfigByItem($item);
        
        if(!is_array($spacialConfig)){
            $spacialConfig = array();
        }
        
        switch ($item->sellFormType) {
            case self::VALUE_TYPE_INPUT_DATETIME:
                $config = array(
                    "image"         => Mage::getDesign()->getSkinUrl("images/grid-cal.gif"),
                    "format"        => $this->_getDateTimeFormat(),
                    "input_format"  => $this->_getDateTimeFormat(),
                    "time"          => true
                );
            break;
            case self::VALUE_TYPE_INPUT_DATE:
                $config = array(
                    "image"         => Mage::getDesign()->getSkinUrl("images/grid-cal.gif"),
                    "format"        => $this->_getDateFormat(),
                    "input_format"  => $this->_getDateFormat()
                );
            break;
            case self::VALUE_TYPE_SELECT:
                $config = array(
                    "options"       => $this->_getSelectOptions($item),
                );
            break;
            case self::VALUE_TYPE_INPUT_RADIO:
                $config = array(
                    "values"        => $this->_getRadioOptions($item),
                    "separator"     => "<br/>"
                );
            break;
            case self::VALUE_TYPE_INPUT_FILE:
                $config = array(); // Overriden type
            break;
            case self::VALUE_TYPE_INPUT_CHECKBOX:
				$config = array(
					"values"        => $this->_getCheckboxOptions($item),
					"name"          => $item->sellFormId."[]"
				);
				
				if (Mage::helper('orbaallegro')->getIsVariantsRequired($item->sellFormId)) {
					$config["separator"] = "<br/>";
					$fidValue = $item->sellFormId;
					$config["after_element_html"] = '<input type="hidden" value='.$fidValue.' name="auction[variants][]" id="auction[variants][]">';
				}
            break;
            case self::VALUE_TYPE_INPUT_TEXT:
            case self::VALUE_TYPE_TEXTAREA:
                $config = array(
                    "maxlength"     => $item->sellFormLength
                );
            break;
        }
        
        $label = $this->_escapeString($item->sellFormTitle);
        $desc = $this->_processHtmlDesc($item);
        
        return array_merge(array(
            'required'      => $this->_checkRequired($item),
            'name'          => $item->sellFormId,
            'value'         => $this->_getItemDefaultValue($item),
            'title'         => $label,
            'note'          => $desc,
            'label'         => $label,
            'allegro_type'  => $item->sellFormType
        ), $config, $spacialConfig);
        
    }
    
    public function parseType($item) {
        
        if(null!==($type=$this->_getSpecialTypeByItem($item))){
            return $type;
        }
        
        switch ($item->sellFormType) {
            case self::VALUE_TYPE_INPUT_DATE:
                $type = "date";
            break;
            case self::VALUE_TYPE_INPUT_DATETIME:
                $type = "date";
            break;
            case self::VALUE_TYPE_SELECT:
                $type = "select";
            break;
            case self::VALUE_TYPE_INPUT_RADIO:
                $type = "radios";
            break;
            case self::VALUE_TYPE_INPUT_CHECKBOX:
                $type = "checkboxes";
				//Required Variants Workaround
				if (Mage::helper('orbaallegro')->getIsVariantsRequired($item->sellFormId)) {
					$type = "radios";
				}
            break;
            case self::VALUE_TYPE_INPUT_FILE:
                $type = "image";
            break;
            case self::VALUE_TYPE_TEXTAREA:
                $type = "textarea";
            break;
            default:
                $type = "text";
            break;
        }
        return $type;
    }

    public function getMetaField($item) {
        $meta = array(
            "sellFormResType"=> $item->sellFormResType
        );
        $field = new Varien_Data_Form_Element_Hidden(array(
            "name" => "meta[" . $item->sellFormId . "]",
            "value" => Zend_Json::encode($meta)
        ));
    }

    protected function _checkRequired($item) {
		if (Mage::helper('orbaallegro')->getIsVariantsRequired($item->sellFormId)) {
			return false;
		}
        return $item->sellFormOpt==self::VALUE_REQUIRED;
    }
    
    protected function _getItemDefaultValue($item) {
        $default = $item->sellFormDefValue;
        switch ($item->sellFormType) {
            case self::VALUE_TYPE_INPUT_FLOAT:
                return (float)$default;
            break;
            case self::VALUE_TYPE_INPUT_NUMBER:
                return (int)$default;
            break;
        }
        
        // Text values 0 is wrong for text values
        return in_array($default, array(0,-1)) ? "" : $default;
    }
    
   
    protected function _getSelectOptions($item) {
        return $this->_extractStandardValues($item);
    }
    
    protected function _getRadioOptions($item) {
        $out = array();
        foreach($this->_extractStandardValues($item) as $k=>$v){
            $out[] = array("label"=>$v, 'value'=>$k);
        }
        return $out;
    }
    
    protected function _getCheckboxOptions($item) {
        $out = array();
        foreach($this->_extractStandardValues($item) as $k=>$v){
            if($v!=="-"){
                $out[] = array("label"=>$v, 'value'=>$k);
            }
        }
        return $out;
    }
    
    protected function _extractStandardValues($item){
        $values = explode("|", $item->sellFormOptsValues);
        $labels = explode("|", $item->sellFormDesc);
        if(count($values)==count($labels)){
            return array_combine($values, $labels);
        }
        return array();
    }
    
    protected function _bitMaskToValues($mask) {
        $out = array();
        for($i=0;$i<7;$i++){
            $pow = pow(2,$i);
            if($mask & $pow){
                $out[] = $pow;
            }
        }
        return $out;
    }
    
    protected function _escapeString($text) {
        return str_replace('\"', '"', $text);
    }
    

    
    protected function _processHtmlDesc($item) {
        if($item->sellFormFieldDesc){
            if(is_numeric($item->sellFormFieldDesc)){
                return '';
            }
            $orig = $this->_escapeString($item->sellFormFieldDesc);
            if(strlen($orig)<=45){
                return $orig;
            }
            $lead = Mage::helper('core/string')->truncate(
                    strip_tags($orig), 45, "...", $a, false);
            $id = "help-$item->sellFormId";
            return  "<span>".
                      $lead.
                         " (<a href=\"#\" onclick=\"$('".$id."').toggle(); return false;\">".Mage::helper('orbaallegro')->__("?")."</a>)".
                    "</span>".
                    "<div id=\"$id\" style=\"display:none; padding: 15px; margin: 10px 20px 0 0; background: #f2f2f2\"><div>".$orig."</div></div>";
        }
        return '';
    }
    

    protected function _getSpecialTypeByItem($item) {
        foreach ($this->_typeMap as $typeName => $map){
            $_map = $this->_getMap();
            if(isset($_map[$item->sellFormId])){
                if(in_array($_map[$item->sellFormId], $map)){
                    return $typeName;
                }
            }
        }
        return null;
    }
    
    protected function _getSpecialConfigByItem($item){
        $_map = $this->_getMap();
        if(isset($_map[$item->sellFormId])){
            $field = $_map[$item->sellFormId];
            if(array_key_exists($field, $this->_configMap)){
                return $this->_configMap[$field];
            }
        }
        return null;
    }
    
    protected function _getRevMap() {
        if(!$this->getData('rev_map')){
            $this->setData('rev_map',array_flip($this->_getMap()));
        }
        return $this->getData('rev_map');
    }
    
    protected function _getMap() {
        if(!$this->getData('map')){
            $this->setData('map',$this->getForm()->getMapping());
        }
        return $this->getData('map');
    }
    
    /**
     * Form passed in costructor
     * @return Orba_Allegro_Model_Form_Abstract
     */
    public function getForm() {
        return parent::getForm();
    }
    
    public static function parsePrice($price) {
        if(is_float($price)){
            return $price;
        }
        $price = preg_replace('/[^\d\s,.]/', '', $price);
        return (float)$price;
    }
    
    public function _processBase64Image($data, $allegroFid,  Orba_Allegro_Model_Auction $auction) {
        $imageMapper = $auction->getImageMapper();
        
        if($imageMapper && is_array($imageMapper)){
            if(isset($imageMapper[$allegroFid])){
                $base = Mage::getBaseDir('media');
                $relativeBin = $imageMapper[$allegroFid].".jpg";
                
                if(!is_file($base . DS . $relativeBin)){
                    @file_put_contents($base . DS . $relativeBin, base64_decode($data));
                }
                
                return $relativeBin;
            }
        }
        return null;
        
    }
}

?>
