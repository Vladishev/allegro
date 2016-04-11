<?php

/* Basic structure **/

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();


/**
 * Allegro services
 */
 $table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/service'))
    ->addColumn("service_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    )) 
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Attribute set Id')    
    ->addColumn("is_production", Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 1
    ))
    ->addColumn("is_supported", Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 0
    ))
    ->addColumn("installed_from_api", Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 0
    ))
    ->addColumn('service_code', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array(
        'nullable'  => false,
    ))
    ->addColumn("service_country_code", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))    
    ->addColumn('service_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
    ))
    ->addColumn('service_currency', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array())
    ->addColumn('service_code_page', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array())
    ->addColumn('service_url', Varien_Db_Ddl_Table::TYPE_TEXT, 128, array())
    ->addColumn('version_key', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array())
    ->addColumn('version_api', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array())
    ->addColumn('version_program', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array())
    ->addColumn('version_category', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array())
    ->addColumn('version_attribute', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array())
    ->addColumn('version_form', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array())
    ->addColumn('version_list', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array())
    ->addIndex($installer->getIdxName('orbaallegro/service', array('attribute_set_id')),
        array('attribute_set_id'))
    ->addIndex($installer->getIdxName('orbaallegro/service', array('service_country_code')),
        array('service_country_code')
     )
    ->addForeignKey(
        $installer->getFkName('orbaallegro/service', 'attribute_set_id', 'eav/attribute_set','attribute_set_id'),
        'attribute_set_id', $installer->getTable('eav/attribute_set'), 'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
 
$installer->getConnection()->createTable($table);


/**
 * Create table 'orbaallegro/eav_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('is_global', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Global')
    ->addColumn('frontend_input_renderer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Frontend Input Renderer')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Visible')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addColumn('is_wysiwyg_enabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is WYSIWYG Enabled')
    /**
     * Alegro metadata
     */
    ->addColumn('allegro_used', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 0,
        ), 'Used by Allegro')
    ->addColumn('allegro_form_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'External form Id')
    ->addColumn('allegro_param_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'External param Id')
     ->addColumn('allegro_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Allegor typ')
    ->addColumn('allegro_meta', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Allegro metadata')
    ->addColumn('allegro_apply_to', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Allegro apply to country')
    ->addForeignKey(
        $installer->getFkName(
             'orbaallegro/eav_attribute', 
             'attribute_id', 
             'eav/attribute', 
             'attribute_id'
         ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Orba Allegro EAV Attribute Table');
$installer->getConnection()->createTable($table);


/**
 * Create base EAV Tables 'orbaallegro/template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/template'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Set ID')
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Allegro country code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Update Time')
    ->addIndex($installer->getIdxName('orbaallegro/template', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('orbaallegro/template', array('attribute_set_id')),
        array('attribute_set_id'))
    ->addForeignKey(
        $installer->getFkName(
            'orbaallegro/template',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id', $installer->getTable('eav/attribute_set'), 'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'orbaallegro/template', 
            'entity_type_id', 
            'eav/entity_type', 
            'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('orbaallegro/template', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Allegro Template Table');
$installer->getConnection()->createTable($table);


/**
 * Create table array('orbaallegro/template', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('orbaallegro/template', 'datetime')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('orbaallegro/template', 'datetime'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'datetime'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'datetime'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'datetime'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'datetime'),
            'attribute_id',
            'eav/attribute',
            'attribute_id'
         ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'datetime'),
            'entity_id',
            'orbaallegro/template',
            'entity_id'
        ),
        'entity_id', $installer->getTable('orbaallegro/template'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'datetime'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Allegro Template Datetime Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('orbaallegro/template', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('orbaallegro/template', 'decimal')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('orbaallegro/template', 'decimal'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'decimal'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'decimal'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'decimal'), array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'decimal'),
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'decimal'),
            'entity_id',
            'orbaallegro/template',
            'entity_id'
        ),
        'entity_id', $installer->getTable('orbaallegro/template'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName(array('orbaallegro/template', 'decimal'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Allegro Template Decimal Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('orbaallegro/template', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('orbaallegro/template', 'int')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('orbaallegro/template', 'int'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'int'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'int'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'int'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'int'),
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'int'),
            'entity_id',
            'orbaallegro/template',
            'entity_id'
        ),
        'entity_id', $installer->getTable('orbaallegro/template'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('orbaallegro/template', 'int'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Allegro Template Integer Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('orbaallegro/template', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('orbaallegro/template', 'text')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('orbaallegro/template', 'text'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'text'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'text'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'text'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(array('orbaallegro/template', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('orbaallegro/template', 'text'), 'entity_id', 'orbaallegro/template', 'entity_id'),
        'entity_id', $installer->getTable('orbaallegro/template'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName(array('orbaallegro/template', 'text'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Allegro Template Text Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('orbaallegro/template', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('orbaallegro/template', 'varchar')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('orbaallegro/template', 'varchar'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'varchar'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'varchar'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('orbaallegro/template', 'varchar'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(array('orbaallegro/template', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('orbaallegro/template', 'varchar'), 'entity_id', 'orbaallegro/template', 'entity_id'),
        'entity_id', $installer->getTable('orbaallegro/template'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('orbaallegro/template', 'varchar'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Allegro Template Varchar Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Allegro types mapper table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/form_options'))
    ->addColumn('mapper_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        ), 'Allegro type')
    ->addColumn('allegro_value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Value ID')
    ->addColumn('allegro_label', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
        ), 'Value ID')
    ->setComment('Value Mapper For Option Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Allegor flat categories
 * 
 * CREATE TABLE IF NOT EXISTS `orba_allegro_category` (
 * `id` int(11) NOT NULL AUTO_INCREMENT,
 * `external_id` int(11) NOT NULL,
 * `name` varchar(255) NOT NULL,
 * `parent` int(11) NOT NULL,
 * `path` varchar(255) NOT NULL,
 * `position` int(11) NOT NULL,
 * `country_id` int(11) NOT NULL,
 * `is_deleted` tinyint(1) NOT NULL,
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/category'))
    ->addColumn("category_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn("external_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
    ))
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ))
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ));
 $installer->getConnection()->createTable($table);      
       

/**
 * Allegro mappings
 */
 $table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/mapping'))
    ->addColumn("mapping_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('entity_id_2', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'nullable'  => false,
    ))
    ->addColumn('attribute_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ))
    ->addColumn('attribute_code_2', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ))
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ))
    ->addIndex($installer->getIdxName('orbaallegro/mapping', array('country_code')),
        array('country_code'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/mapping', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);  


/**
 * Allegro mapping in stores
 */

 $table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/mapping_store'))
    ->addColumn("mapping_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/mapping_store', 'mapping_id', 'orbaallegro/mapping', 'mapping_id'),
        'mapping_id', $installer->getTable('orbaallegro/mapping'), 'mapping_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('orbaallegro/mapping_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);  

/**
 * Allegro Auction
 */
     
 $table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/auction'))
    ->addColumn("auction_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('allegro_auction_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'nullable'  => false,
    ))
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    ->addColumn('shop_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullable' => false))
    ->addColumn('currency', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array('nullable' => false))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array("unsigned"=>true))
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('items_placed', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array("unsigned"=>true, "nullable"=>false))
    ->addColumn('items_sold', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array("unsigned"=>true, "nullable"=>false))
    ->addColumn('auction_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))
    ->addColumn('auction_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => false))
    ->addColumn('allegro_seller_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false))    
    ->addColumn('seller_login', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => false))
    ->addColumn('seller_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => false))
    ->addColumn('auction_cost', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array('nullable'  => false))
    ->addColumn('auction_duration', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))
    ->addColumn('auction_item_price', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array('nullable'  => false))
    ->addColumn('auction_additional_options', Varien_Db_Ddl_Table::TYPE_SMALLINT, 2, array("unsigned"=>true))
    ->addColumn('allegro_starting_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array("unsigned"=>true))
    ->addColumn('allegro_auction_info', Varien_Db_Ddl_Table::TYPE_TEXT, 100)
    ->addColumn('ending_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Ending Time')
    ->addColumn('closed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Close Time')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex($installer->getIdxName('orbaallegro/auction', array('country_code')),
        array('country_code'))
    ->addIndex($installer->getIdxName('orbaallegro/auction', array('template_id')),
        array('template_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction', array('product_id')),
        array('product_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction', array('category_id')),
        array('category_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction', array('shop_category_id')), 
        array('shop_category_id'))     
    ->addIndex($installer->getIdxName('orbaallegro/auction', 
        array('parent_id')), array('parent_id'))     
    ->addIndex($installer->getIdxName('orbaallegro/auction', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction', 'template_id', 'orbaallegro/template', 'entity_id'),
        'template_id', $installer->getTable('orbaallegro/template'), 'entity_id')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
         Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction', 'category_id', 'orbaallegro/category', 'category_id'),
        'category_id', $installer->getTable('orbaallegro/category'), 'category_id')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction', 'shop_category_id', 'orbaallegro/shop_category', 'shop_category_id'),
        'shop_category_id', $installer->getTable('orbaallegro/shop_category'), 'category_id')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id');
$installer->getConnection()->createTable($table);  

/**
 * Allegro Auction serialized data
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/auction_serialized'))
    ->addColumn('auction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'=>false))
    ->addColumn('serialized_data', Varien_Db_Ddl_Table::TYPE_TEXT, 1024*1024*10, array('nullable'  => false))
    ->addIndex($installer->getIdxName('orbaallegro/auction_serialized', array('auction_id')),
        array('auction_id'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction_serialized', 'auction_id', 'orbaallegro/auction', 'auction_id'),
        'auction_id', $installer->getTable('orbaallegro/auction'), 'auction_id',
         Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
     );
$installer->getConnection()->createTable($table);  


/**
 * Allegro Transaction
 */
 $table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/transaction'))
    ->addColumn("transaction_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('shipment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('payment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('currency', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array())
    ->addColumn('allegro_transaction_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false))
    ->addColumn('allegro_seller_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false))    
    ->addColumn('allegro_buyer_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false))    
    ->addColumn('allegro_shipment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))    
    ->addColumn('allegro_payment_type', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))               
    ->addColumn('allegro_payu_transaction_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array("unsigned"=>true))
    ->addColumn('allegro_payu_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array())
    ->addColumn('allegro_payu_start_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array())
    ->addColumn('allegro_payu_end_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array())
    ->addColumn('allegro_payu_cancel_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array())
    ->addColumn('transaction_subtotal', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array("nullable"=>false))
    ->addColumn('transaction_cod_amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array("nullable"=>false))
    ->addColumn('transaction_total', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array("nullable"=>false))
    ->addColumn('seller_login', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array("nullable"=>false, 'defualt'=>0))
    ->addColumn('seller_email', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array("nullable"=>false, 'defualt'=>0))
    ->addColumn('buyer_login', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array("nullable"=>false, 'defualt'=>0))
    ->addColumn('buyer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array("nullable"=>false, 'defualt'=>0))
    ->addColumn('buyer_invoice', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array("nullable"=>false, 'defualt'=>0))
    ->addColumn('buyer_message', Varien_Db_Ddl_Table::TYPE_TEXT, 1024, array("nullable"=>false, 'defualt'=>0))
    ->addColumn('is_ignored', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')    
    ->addColumn('allegro_created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Allegro Creation Time')
    ->addIndex($installer->getIdxName('orbaallegro/transaction', array('country_code')),
        array('country_code'))
    ->addIndex($installer->getIdxName('orbaallegro/transaction', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('orbaallegro/transaction', array('shipment_id')),
        array('shipment_id'))
    ->addIndex($installer->getIdxName('orbaallegro/transaction', array('payment_id')),
        array('payment_id'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction', 'shipment_id', 'orbaallegro/mapping_shipment', 'shipment_id'),
        'shipment_id', $installer->getTable('orbaallegro/mapping_shipment'), 'shipment_id')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction', 'payment_id', 'orbaallegro/mapping_payment', 'payment_id'),
        'payment_id', $installer->getTable('orbaallegro/mapping_payment'), 'payment_id');
$installer->getConnection()->createTable($table);  


/**
 * Allegro Transaction address
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/transaction_address'))
    ->addColumn("address_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'=>false))
    ->addColumn('pickpoint_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('same_as_billing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array('nullable'  => false))           
    ->addColumn('fullname', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array())           
    ->addColumn('company', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array())           
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullabel'=>false))
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))      
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))                
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => false))           
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))           
    ->addColumn('vatid', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array())           
    ->addColumn('pickpoint_info', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array())           
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex($installer->getIdxName('orbaallegro/transaction_address', array('transaction_id')),
        array('transaction_id'))
    ->addIndex($installer->getIdxName('orbaallegro/transaction_address', array('pickpoint_id')),
        array('pickpoint_id'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction_address', 'transaction_id', 'orbaallegro/transaction', 'transaction_id'),
        'transaction_id', $installer->getTable('orbaallegro/transaction'), 'transaction_id',
         Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction_address', 'pickpoint_id', 'orbaallegro/pickpoint', 'pickpoint_id'),
        'pickpoint_id', $installer->getTable('orbaallegro/pickpoint'), 'pickpoint_id', 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);  

/**
 * Allegro Transaction auction
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/transaction_auction'))
    ->addColumn("item_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'=>false))
    ->addColumn('auction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))
    ->addColumn('quantity', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array("nullable"=>false)) 
    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array("nullable"=>false))
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array('nullable'  => false))
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array('nullable'  => false))
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex($installer->getIdxName('orbaallegro/transaction_auction', array('transaction_id')),
        array('transaction_id'))
    ->addIndex($installer->getIdxName('orbaallegro/transaction_auction', array('auction_id')),
        array('auction_id'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction_auction', 'transaction_id', 'orbaallegro/transaction', 'transaction_id'),
        'transaction_id', $installer->getTable('orbaallegro/transaction'), 'transaction_id',
         Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction_auction', 'auction_id', 'orbaallegro/auction', 'auction_id'),
        'auction_id', $installer->getTable('orbaallegro/auction'), 'auction_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);  


/**
 * Allegro Transaction serialized data
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/transaction_serialized'))
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'=>false))
    ->addColumn('serialized_data', Varien_Db_Ddl_Table::TYPE_TEXT, 1024*1024*10, array('nullable'  => false))
    ->addIndex($installer->getIdxName('orbaallegro/transaction_serialized', array('transaction_id')),
        array('transaction_id'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/transaction_serialized', 'transaction_id', 'orbaallegro/transaction', 'transaction_id'),
        'transaction_id', $installer->getTable('orbaallegro/transaction'), 'transaction_id',
         Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
     );
$installer->getConnection()->createTable($table);  

/**
 * Allegro pick points providers
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/pickpoint_provider'))
    ->addColumn("provider_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('provider_name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array('nullable'  => false))  
    ->addColumn('local_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))         
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addForeignKey(
        $installer->getFkName('orbaallegro/pickpoint_provider', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code'
     );
$installer->getConnection()->createTable($table);

/**
 * Allegro pick points
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/pickpoint'))
    ->addColumn("pickpoint_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('provider_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))  
    ->addColumn('pickpoint_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))           
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))           
    ->addColumn('fullname', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array('nullable'  => false))           
    ->addColumn('county_code', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullabel'=>false))
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))      
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array('nullable'  => false))                
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable'  => false))                   
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex($installer->getIdxName('orbaallegro/pickpoint', array('pickpoint_code')), array('pickpoint_code'))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/pickpoint', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code'
     )
     ->addForeignKey(
        $installer->getFkName('orbaallegro/pickpoint', 'provider_id', 'orbaallegro/pickpoint_provider', 'provider_id'),
        'provider_id', $installer->getTable('orbaallegro/pickpoint_provider'), 'provider_id'
     );
$installer->getConnection()->createTable($table);


/**
 * Allegro shipments
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/mapping_shipment'))
    ->addColumn("shipment_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('shipment_map', Varien_Db_Ddl_Table::TYPE_TEXT, 50)           
    ->addColumn('can_change', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullable'  => false, 'defualt'=>1))           
    ->addColumn('allegro_shipment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))           
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))           
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array('nullable'  => false))           
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullabel'=>false))
    ->addColumn('time_from', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array())      
    ->addColumn('time_to', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array())   
    ->addColumn('is_pickpoint', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('orbaallegro/mapping_shipment'),
            array('allegro_shipment_id', 'country_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('allegro_shipment_id', 'country_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName('orbaallegro/mapping_shipment', 'country_code', 'orbaallegro/service', 'service_country_code'),
        'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code'
     );
$installer->getConnection()->createTable($table);


/**
 * Allegro payment
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/mapping_payment'))
    ->addColumn("payment_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))    
    ->addColumn('payment_map', Varien_Db_Ddl_Table::TYPE_TEXT, 50)
    ->addColumn('allegro_payment_type', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array('nullable'  => false))           
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false))           
    ->addColumn('price_from', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array())           
    ->addColumn('price_to', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array())           
    ->addColumn('cancel_time', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array())           
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array('nullable'  => false))   
    ->addColumn('is_payu', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))        
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('default'=>0))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('orbaallegro/mapping_payment'),
            array('allegro_payment_type', 'country_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('allegro_payment_type', 'country_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));
    // /* key is only after first import */
    //->addForeignKey(
    //    $installer->getFkName('orbaallegro/mapping_payment', 'country_code', 'orbaallegro/service', 'service_country_code'),
    //    'country_code', $installer->getTable('orbaallegro/service'), 'service_country_code'
    // );
$installer->getConnection()->createTable($table);


/**
 * Allegro form fields
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/mapping_sellform'))
    ->addColumn("field_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn("external_id", Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
    ))
   
    ->addColumn('local_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ))
     ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ));
 $installer->getConnection()->createTable($table);   
 
 
/**
 * Allegor flat categories
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('orbaallegro/shop_category'))
    ->addColumn("category_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn("external_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('user_login', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
    ))
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('is_deleted', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
    ))
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ))
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ));
 $installer->getConnection()->createTable($table);    


/**
 * Allegro components in system tables
 * @todo Add Indexes and contstraints
 */

$conn = $installer->getConnection();
$transactionId = "orbaallegro_transaction_id"; // Int
$shipmentId = "orbaallegro_shipment_id"; // Int
$paymentId = "orbaallegro_payment_id"; // Int
$auctionId = "orbaallegro_auction_id"; // Int
$addressId = "orbaallegro_address_id"; // Int
$buyerId = "orbaallegro_allegro_buyer_id"; // Long (allegro id)
$countryCode = "orbaallegro_country_code"; // Varchar

// Transaction
$conn
    ->addColumn($installer->getTable('sales/quote'), $transactionId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$transactionId
    ));

$conn->addColumn($installer->getTable('sales/order'), $transactionId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$transactionId
    ));


// Shipment
$conn
    ->addColumn($installer->getTable('sales/quote'), $shipmentId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$shipmentId
    ));

$conn->addColumn($installer->getTable('sales/order'), $shipmentId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$shipmentId
    ));

$conn
    ->addColumn($installer->getTable('sales/quote_address'), $shipmentId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$shipmentId
    ));

$conn->addColumn($installer->getTable('sales/order_address'), $shipmentId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$shipmentId
    ));


// Payment
$conn
    ->addColumn($installer->getTable('sales/quote'), $paymentId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$paymentId
    ));

$conn->addColumn($installer->getTable('sales/order'), $paymentId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$paymentId
    ));

// Address
$conn
    ->addColumn($installer->getTable('sales/quote_address'), $addressId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$addressId
    ));

$conn->addColumn($installer->getTable('sales/order_address'), $addressId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$addressId
    ));


// Auction id
$conn
    ->addColumn($installer->getTable('sales/quote_item'), $auctionId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$auctionId
    ));

$conn->addColumn($installer->getTable('sales/order_item'), $auctionId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$auctionId
    ));

// Buyer id
$conn
    ->addColumn($installer->getTable('sales/quote'), $buyerId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
        'comment'=>$buyerId
    ));

$conn->addColumn($installer->getTable('sales/order'), $buyerId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
        'comment'=>$buyerId
    ));

$conn
    ->addColumn($installer->getTable('sales/quote_address'), $buyerId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
        'comment'=>$buyerId
    ));

$conn->addColumn($installer->getTable('sales/order_address'), $buyerId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_BIGINT,
        'comment'=>$buyerId
    ));

// Country code
$conn
    ->addColumn($installer->getTable('sales/quote'), $countryCode, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$countryCode
    ));

$conn->addColumn($installer->getTable('sales/order'), $countryCode, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$countryCode
    ));


$installer->endSetup();
// Install entites & componets
$installer->installEntities();