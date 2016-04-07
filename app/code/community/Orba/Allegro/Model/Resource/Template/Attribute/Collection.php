<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product EAV additional attribute resource collection
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Orba_Allegro_Model_Resource_Template_Attribute_Collection
    extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('orbaallegro/resource_eav_attribute', 'eav/entity_attribute');
    }

    /**
     * initialize select object
     *
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected function _initSelect()
    {
        $entityTypeId = (int)Mage::getModel('eav/entity')->setType(Orba_Allegro_Model_Template::ENTITY)->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = array();
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = Mage::getResourceHelper('core')->castField('main_table.'.$labelColumn);
            }
        }
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), $retColumns)
            ->join(
                array('additional_table' => $this->getTable('orbaallegro/eav_attribute')),
                'additional_table.attribute_id = main_table.attribute_id'
                )
            ->where('main_table.entity_type_id = ?', $entityTypeId);
        return $this;
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $typeId
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }

    /**
     * Return array of fields to load attribute values
     *
     * @return array
     */
    protected function _getLoadDataFields()
    {
        $fields = array_merge(
            parent::_getLoadDataFields(),
            array(
                'additional_table.is_global',
                'additional_table.is_wysiwyg_enabled',
                'additional_table.position',
                'additional_table.allegro_used',
                'additional_table.allegro_param_id',
                'additional_table.allegro_form_id',
                'additional_table.allegro_type',
                'additional_table.allegro_meta',
                'additional_table.allegro_apply_to',
            )
        );

        return $fields;
    }

}
