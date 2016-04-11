<?php
class Orba_Allegro_Model_Shop_Category extends Orba_Allegro_Model_Abstract {
    
    
    const CACHE_ID_FLAT = 'allegro_shop_categories_flat';
    const CACHE_ID_TREE = 'allegro_shop_categories_array';
    
    /**
     * Array of all external ids found in import.
     * 
     * @var array
     */
    protected $externalIds = array();
    
    /**
     * Array of id paths to categories.
     * 
     * @var array 
     */
    protected $paths = array();
    
    /**
     * Import results array.
     * 
     * @var array
     */
    protected $importRes = array();
    
    protected $namePaths = array();

    protected $_resourceName = "orbaallegro/shop_category";
    protected $_resourceCollectionName = "orbaallegro/shop_category_collection";
    

    public function doImport() {
        //$countryIds = $this->getConfig()->getCategoriesSyncCountryIds();
        $fullConf = $this->_getConfig()->getFullConfig();
        $servicesToMap = array();
        
        foreach($fullConf as $conf){
            if((int)$conf['categories']['active']){
                $servicesToMap[$conf['config']['country_code']] = $conf['config'];
            }
        }
        
        if (count($servicesToMap)) {
            foreach ($servicesToMap as $serviceConf){
                $client = Mage::getModel('orbaallegro/client');
                /* @var $client Orba_Allegro_Model_Client */
                $client->setData($serviceConf);
                $login = $client->getLogin();
                $countryId = $serviceConf['country_code'];
                $result = false;
                try {
                    $result = $client->getShopCatsData();
                    usleep(50);
                } catch(Exception $e){
                    Mage::logException($e);
                }
                if($result) {
                    $this->externalIds[$countryId] = array();
                    $this->importRes[$countryId] = array(
                        'created' => 0,
                        'updated' => 0,
                        'deleted' => 0
                    );
                    if(!is_object($result->shopCatsList->item[0])){
                        $result->shopCatsList->item = array($result->shopCatsList->item);
                    }

                    $this->parseAndSave($result->shopCatsList->item, $countryId, $login);
                }
            }
        }
        return $this->importRes;
    }
    
    protected function parseAndSave($categories, $countryId, $login) {
        $omitedChildren = array();
        foreach ($categories as $category) {
            if(!is_object($category)){
                continue;
            }
            $externalId = (int)$category->catId;
            if ($externalId) {
                $name = (string)$category->catName;
                $position = (int)$category->catPosition;
                $parent = (int)$category->catParent;
                $this->externalIds[$countryId][] = $externalId;
                $_category = $this->loadByAttribute('external_id', $externalId);
                if ($parent) {
                    if (isset($this->paths[$countryId][$parent])) {
                        $path = $this->paths[$countryId][$parent];
                    } else {
                        $_parent = $this->loadByAttribute('external_id', $parent);
                        if ($_parent) {
                            $path = $_parent->getPath();
                            $this->paths[$countryId][$parent] = $path;
                        } else {
                            $omitedChildren[$parent] = $category;
                            continue;
                        }
                    }
                } else {
                    $path = '';
                }
                if (!$_category) {
                    /**
                     * @todo zoptymalizować proces zapisu szczególnie ścieżek
                     * Można to zrobić przez update wszsytkich sciezek po zapisie
                     * lub triggerem przy insertowaniu
                     */
                    $this->setData(array(
                        'category_id' => null,
                        'parent_id' => $parent,
                        'external_id' => $externalId,
                        'position' => $position,
                        'name' => $name,
                        'country_id' => $countryId,
                        'user_login' => $login
                    ))->save()->setPath($this->getNewPath($path, $this->getId()))->save();
                    $this->importRes[$countryId]['created']++;
                    
                    // Mage::log('Shop cateogory "'.$login.' - '.$name.'" added', null, 'allegro.log');
                    
                    if (isset($omitedChildren[$externalId]) && !empty($omitedChildren[$externalId])) {
                        $this->parseAndSave($omitedChildren[$externalId], $countryId, $login);
                        unset($omitedChildren[$externalId]);
                    }
                } else {
                    $newPath = $this->getNewPath($path, $_category->getId());
                    if ($_category->getName() != $name || $_category->getPath() != $newPath || $_category->getPosition() != $position) {
                        $_category->setName($name)
                                ->setPath($newPath)
                                ->setPosition($position)
                                ->setIsDeleted(0)
                                ->setUserLogin($login)
                                ->save();
                        // Mage::log('Shop cateogory "'.$login.' - '.$name.'" updated', null, 'allegro.log');
                        $this->importRes[$countryId]['updated']++;
                    }
                }
            }
        }
        if (!empty($this->externalIds)) {
            $collection = $this->getCollection()
                    ->addFieldToFilter('external_id', array('nin' => $this->externalIds[$countryId]))
                    ->addFieldToFilter('country_id', $countryId)
                    ->addFieldToFilter('user_login', $login)
                    ->addFieldToFilter('is_deleted', 0);
            foreach ($collection as $item_to_delete) {
                $item_to_delete->setIsDeleted(1)
                        ->save();
                $this->importRes[$countryId]['deleted']++;
            }
        }
    }
    
    protected function getNewPath($start, $id) {
        return $start.(empty($start) ? '' : '/').$id;
    }
    
    public function getRequestCountryId() {
        $request = Mage::app()->getRequest();
        $store = $request->getParam("store");
        $website = $request->getParam("website");
        return $this->_getConfig()->getCountryCode($store, $website);
    }
    
    public function getAllOptions($flat = true, $empty = true, $login) {
        $country_id = $this->getRequestCountryId();
        if ($flat) {
            $cache_id = self::CACHE_ID_FLAT.$country_id.$login;
            if (false !== ($data = Mage::app()->getCache()->load($cache_id))) {
                $options = unserialize($data);
            } else {
                $options = $this->getFlatTree(null, $country_id, $login);
                Mage::app()->getCache()->save(serialize($options), $cache_id, array('allegro'), 60 * 60 * 24);
            }
            if ($empty) {
                $options = array_merge(array(array('label' => '', 'value' => '')), $options);
            }
        } else {
            $cache_id = self::CACHE_ID_TREE.$country_id.$login;
            if (false !== ($data = Mage::app()->getCache()->load($cache_id))) {
                $options = unserialize($data);
            } else {
                $options = $this->getTree(null, $country_id, $login);
                Mage::app()->getCache()->save(serialize($options), $cache_id, array('allegro'), 60 * 60 * 24);
            }
        }
        return $options;
    }
    
    public function getFlatTree($parent = null, $country_id = null, $login) {
        
        $res = array();
        $parent_id = ($parent === null) ? 0 : $parent->getExternalId();
        $category_collection = $this->getCollection()
            ->addFieldToFilter('parent_id', $parent_id)
            ->addFieldToFilter('user_login', $login)
            ->setOrder('name', 'asc');
        if ($country_id) {
            $category_collection->addFieldToFilter('country_id', $country_id);
        }
        foreach ($category_collection as $category) {
            if ($parent === null) {
                $category->setNamePath($category->getName());
            } else {
                $category->setNamePath($parent->getNamePath().' / '.$category->getName());
            }
            $res[] = array(
                'label' => ($category->getIsDeleted() ? '['.Mage::helper('orbaallegro')->__('Deleted').'] ' : '').$category->getNamePath(),
                'value' => $category->getId()
            );
            $res = array_merge($res, $this->getFlatTree($category, $country_id, $login));
        }
        return $res;
    }
    
    public function getTree($parent = null, $country_id = null, $login) {
        $res = array();
        $parent_id = ($parent === null) ? 0 : $parent->getExternalId();
        $category_collection = $this->getCollection()
            ->addFieldToFilter('parent_id', $parent_id)
            ->addFieldToFilter('user_login', $login)
            ->setOrder('name', 'asc');
        if ($country_id) {
            $category_collection->addFieldToFilter('country_id', $country_id);
        }
        foreach ($category_collection as $category) {
            if ($parent === null) {
                $category->setNamePath($category->getName());
            } else {
                $category->setNamePath($parent->getNamePath().' / '.$category->getName());
            }
            $res[$category->getId()] = array(
                'label' => ($category->getIsDeleted() ? '['.Mage::helper('orbaallegro')->__('Deleted').'] ' : '').$category->getNamePath(),
                'value' => $category->getId(),
                'children' => $this->getTree($category, $country_id, $login)
            );
        }
        return $res;
    }
    
    public function getChildren($parent_id = 0, $country_id, $login, $useExternal=false) {
        if (!$country_id) {
            $country_id = Orba_Allegro_Model_Service::ID_ALLEGROPL;
        }
        $res = array();
        if ($parent_id) {
            // Should use external id?
            if($useExternal){
                $model = Mage::getModel("orbaallegro/shop_category")->load($parent_id, "external_id");
                if($model->getId()){
                    $parent_id = $model->getId();
                }
            }
            
            $model = $this->load($parent_id);
            
            if ($model->getId()) {
                $external_parent_id = $model->getExternalId();
            } else {
                return $res;
            }
        } else {
            $external_parent_id = 0;
        }
        $collection = $this->getCollection()
                ->addFieldToFilter('parent_id', $external_parent_id)
                ->addFieldToFilter('country_id', $country_id)
                ->addFieldToFilter('is_deleted', 0)
                ->addFieldToFilter('user_login', $login)
                ->addFieldToSelect(array('category_id', 'name', 'external_id'))
                ->setOrder('position', 'ASC');
        foreach ($collection as $category) {
            $res[$useExternal ? $category->getExternalId() : $category->getId()] = $category->getName();
        }
        return $res;
    }
    
    public function getNamePath($id) {
        if (!isset($this->namePaths[$id])) {
            $name_path = '';
            $model = $this->load($id);
            if ($model->getId()) {
                $path = explode('/', $model->getPath());
                foreach ($path as $path_id) {
                    $category = $this->load($path_id);
                    $name_path .= $category->getName();
                    if ($path_id != $id) {
                        $name_path .= ' / ';
                    }
                }
            }
            $this->namePaths[$id] = $name_path;
        }
        return $this->namePaths[$id];
    }
    
    public function loadByAttribute($attribute, $value) {
        $collection = $this->getCollection()
            ->addFieldToFilter($attribute, $value);
        $first = $collection->getFirstItem();
        if ($first->getId()) {
            return $first;
        }
        return false;
    }
    
       
}