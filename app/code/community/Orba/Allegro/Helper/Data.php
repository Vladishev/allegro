<?php

class Orba_Allegro_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_apis = array();
    
    protected $_countryLabels = null;
    
    protected $_countryMap = array(
        
    );
	
    //XML Config Paths
	const XML_PATH_IS_DEBUG_MODE			= "orbaallegro/utils/debug_mode";
	const XML_PATH_UNHANDELED_ITEM_COUNT	= "orbaallegro/utils/unhandled_item_count";
	
    // Max images sizes
    const IMAGE_WIDTH = 590;
    const IMAGE_HEIGHT = 590;
	
	protected $_variantsRequiredFidIds = array(
		23613,
		23629,
		23630,
		23631,
		23632,
		23633,
		23634,
		23635,
		23636,
		23637,
		23638,
		23639,
		23640,
		23641,
		23642,
		23643,
		23644,
		23645,
	);
	
	const ALLEGRO_VARIANTS = 'variants';


	/**
     * Gets Orba Allegro cache object.
     * @return Zend_Cache_Core
     */
    public function getCache() {
        return Mage::getModel("orbaallegro/client_cache")->getFrontend();
    }
    
    /**
     * Gets extension config model singleton.
     * @return Orba_Allegro_Model_Config
     */
    public function getConfig() {
        return Mage::getSingleton('orbaallegro/config');
    }
    
    /**
     * Is Orba Allegro currently instaled
     * @return boolean
     */
    public function isInstalled() {
        return Mage::getResourceModel("core/resource")->getDbVersion("orbaallegro_setup");
    }
    
    /**
     * Has imported data
     * return boolean
     */
    public function isFirstImportComplete() {
        return $this->getConfig()->isFirstImportComplete();
    }
    
    /**
     * Is debug mode? 
     * @todo Implement
     * @return boolean
     */
    public function getIsDebugMode() {
        return true;
    }

/**
     * Set import completed
     */
    public function setFirstImportComplete() {
        $this->getConfig()->setFirstImportComplete();
    }
    
    /**
     * Can login to service
     * @return boolean
     */
    public function canLogin($storeId, $websiteId=null, $loginData=array()){
        $client = Mage::getModel("orbaallegro/client");
        $client->setStoreId($storeId);
        $client->setWebsiteId($websiteId);
        if(!empty($loginData)){
            $client->setData($loginData);
        }
        $resp = false;
        try{
            $resp = $client->getUserSession(true);
        }catch(Orba_Allegro_Model_Client_Exception $e){
            Mage::helper("orbaallegro/log")->log($e->getMessage());
        }  catch (Exception $e){
            Mage::logException($e);
        }
        return (bool)$resp;
    }
    
    
    public function getBlockWidth() {
        return $this->getConfig()->getImagesWidth();
    }
    
    public function getBlockHeight() {
        return $this->getConfig()->getImagesHeight();
    }
    
    public function getKeepFrame() {
        return $this->getConfig()->getImagesKeepFrame();
    }
    
    public function getCountryLabel($coutryCode) {
        if(!is_array($this->_countryLabels)){
            $model = Mage::getModel("orbaallegro/system_config_source_country");
            /* @var $model Orba_Allegro_Model_System_Config_Source_Country */
            $this->_countryLabels = $model->toOptionHash();
        }
        
        if(isset($this->_countryLabels[$coutryCode])){
            return $this->_countryLabels[$coutryCode];
        }
        
        return $coutryCode;
    }
    
    public function getCountryMapped($code) {
        return Mage::getSingleton('orbaallegro/mapping_country')->getCountryMapped($code);
    }
    
    public function getAuctionFilesPath() {
        return  "orbaallegro" . DS . "auction";
    }
    
    public function getTransactionRowClass($item) {
        $classes = array();
        if(!$item->getOrderId() && !$item->getIsIgnored() && !$item->getIsDeleted()){
            $classes[] = "noticed " . $item->getIsDeleted();
        }
        if($item->getIsIgnored()){
            $classes[] = "ignored";
        }
        if($item->getIsDeleted()){
            $classes[] = "striked";
        }
        return count($classes) ? join(" ", $classes) : null;
    }
	
	public function rebuildAuctionFormHtml($html) {
                $trs = explode('<td class="label">', $html);
                foreach ($trs as $i => $tr) {
                    if (strpos($tr, 'pierwsza sztuka') !== false || strpos($tr, 'kolejna sztuka') !== false || strpos($tr, 'ilość w paczce') !== false) {
                        $trs[$i] = preg_replace('/\s+/', ' ', $tr);
                    }
                }
                $html = implode('<td class="label">', $trs);
		$header = '<tr><td class="label"></td><td class="value"><div style="display: inline-block; width: 106px;">Pierwsza sztuka</div> <div style="display: inline-block; width: 106px;">Kolejna sztuka</div> <div style="display: inline-block; width: 106px;">Ilość w paczce</div></td></tr>';
		$html = preg_replace('/<tr>\s+<td class="label"><label for="auction([0-9]+)">([^(<]+) \(pierwsza sztuka\)/', $header . '<tr> <td class="label"><label for="auction\\1">\\2 (pierwsza sztuka)', $html, 1);
		$html = preg_replace_callback('/auction([0-9]+)">([^(<]+) \(pierwsza sztuka\)<\/label><\/td> <td class="value"> <input id="[^"]+" name="[^"]+" value="([^"]+)" title="[^<]+<\/td> <\/tr> <tr> <td colspan="2" class="hidden"><input id="[^"]+" name="[^"]+" value="([^"]+)" type="hidden"\/><\/td> <\/tr> <tr> <td colspan="2" class="hidden"><input id="[^"]+" name="[^"]+" value="([^"]+)" type="hidden"\/><\/td> <\/tr> <tr> <td class="label"><label for="auction([0-9]+)">[^(<]+ \(kolejna sztuka\)<\/label><\/td> <td class="value"> <input id="[^"]+" name="[^"]+" value="([^"]+)" title="[^>]+> <\/td> <\/tr> <tr> <td colspan="2" class="hidden"><input id="[^"]+" name="[^"]+" value="([^"]+)" type="hidden"\/><\/td> <\/tr> <tr> <td colspan="2" class="hidden"><input id="[^"]+" name="[^"]+" value="([^"]+)" type="hidden"\/><\/td> <\/tr> <tr> <td class="label"><label for="auction([0-9]+)">[^(]+ \(ilość w paczce\)<\/label><\/td> <td class="value"> <input id="[^"]+" name="[^"]+" value="([^"]+)" title="[^>]+> <\/td> <\/tr> <tr> <td colspan="2" class="hidden"><input id="[^"]+" name="[^"]+" value="([^"]+)" type="hidden"\/><\/td> <\/tr> <tr> <td colspan="2" class="hidden"><input id="[^"]+" name="[^"]+" value="([^"]+)" type="hidden"\/><\/td> <\/tr>/', function($matches) {
			return '<tr> <td class="label"><label for="auction' . $matches[1] . '">' . $matches[2] . '</label></td> <td class="value"> <input style="width:100px;" id="auction' . $matches[1] . '" name="auction[' . $matches[1] . ']" value="' . $matches[3] . '" title="Pierwsza sztuka" type="text" class=" input-text input-text"/> <input style="width:100px;" id="auction' . $matches[6] . '" name="auction[' . $matches[6] . ']" value="' . $matches[7] . '" title="Kolejna sztuka" type="text" class=" input-text input-text"/> <input style="width:100px;" id="auction' . $matches[10] . '" name="auction[' . $matches[10] . ']" value="' . $matches[11] . '" title="Ilość w paczce" type="text" class=" input-text input-text"/>                            </td> </tr> <tr> <td colspan="2" class="hidden"><input id="auction' . $matches[1] . '_res" name="auction[res][' . $matches[1] . ']" value="' . $matches[4] . '" type="hidden"/><input id="auction' . $matches[1] . '_opt" name="auction[opt][' . $matches[1] . ']" value="' . $matches[5] . '" type="hidden"/>                 <input id="auction' . $matches[6] . '_res" name="auction[res][' . $matches[6] . ']" value="' . $matches[8] . '" type="hidden"/><input id="auction' . $matches[6] . '_opt" name="auction[opt][' . $matches[6] . ']" value="' . $matches[9] . '" type="hidden"/></td>                  <input id="auction' . $matches[10] . '_res" name="auction[res][' . $matches[10] . ']" value="' . $matches[12] . '" type="hidden"/><input id="auction' . $matches[10] . '_opt" name="auction[opt][' . $matches[10] . ']" value="' . $matches[13] . '" type="hidden"/></td> </tr>';
		}, $html);
		return $html;
	}
	
	/**
	 * Get Parent Product Ids based on Product Type and Product Id
	 * 
	 * @param int		$productId		Product Id
	 * @param string	$productType	Product Type
	 * 
	 * @return mixed array | boolean Parent Product Ids
	 */
	public function getParentIds($productId, $productType) {
		switch ($productType) {
			case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
				$productIds = array($productId);
				break;
			case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
				$productIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($productId);
				break;
			default:
				$productIds = null;
				break;
		}
		
		return $productIds;
	}
	
	/**
	 * Get array of Product Types that can be used for Allegro Auctions
	 * 
	 * @return array Product Types to use with Allegro Auctions
	 */
	public function getAllowedAuctionProductTypes()
	{
		$productTypes = array(
			Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
			Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
		);

		return $productTypes;
	}
	
	/*
	 * Check if Form Item needs Required Varaints Attribute
	 */
	public function getIsVariantsRequired($sellFormId) {
		$required = false;
		
		if (in_array($sellFormId, $this->_variantsRequiredFidIds)) {
			$required = true;
		}
		
		return $required;
	}
	
    /**
     * Check Ajax Loader Flag for Unhandled Item Count
	 * 
     * @return bool
     */
    public function useAjaxForUnhandledItemCount() {
        return (bool)Mage::getStoreConfig(self::XML_PATH_UNHANDELED_ITEM_COUNT);
    }	
}