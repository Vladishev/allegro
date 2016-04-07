<?php

/* v.0.0.0.3 */

$installer = $this;
/* @var $installer Orba_Allegro_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Interface */

/**
 * Add auction bids
 */

$auctionBidTable = $installer->getTable('orbaallegro/auction_bid');

$table = $connection
    ->newTable($auctionBidTable)
    ->addColumn("bid_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    // Bid data
    ->addColumn('auction_id',           Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    ->addColumn('contractor_id',        Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('allegro_auction_id',   Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable' => false))
    ->addColumn("allegro_user_id",      Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false))
    //->addColumn('transaction_id',       Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    //->addColumn('order_id',             Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('bid_status',           Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('nullable'  => false, 'default'=>0))
    ->addColumn('buyer_login',          Varien_Db_Ddl_Table::TYPE_TEXT, 100, array('nullable' => false))
    ->addColumn('buyer_status',         Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('nullable'  => false, 'default'=>0))
    ->addColumn('seller_login',         Varien_Db_Ddl_Table::TYPE_TEXT, 100, array('nullable' => false))
    ->addColumn('item_price',           Varien_Db_Ddl_Table::TYPE_FLOAT, null, array('nullable' => false))
    ->addColumn('item_quantity',        Varien_Db_Ddl_Table::TYPE_INTEGER, 150, array('nullable' => false))
    ->addColumn('cancel_status',        Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('nullable'  => false, 'default'=>0))
    ->addColumn('cancel_reason',        Varien_Db_Ddl_Table::TYPE_TEXT, 255, array())
    // Misc
    ->addColumn('is_ignored',           Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('nullable'  => false, 'default'=>0))
    ->addColumn('is_deleted',           Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array('nullable'  => false, 'default'=>0))
    ->addColumn('local_info',           Varien_Db_Ddl_Table::TYPE_TEXT, 255, array())
    // Dates
    ->addColumn('allegro_canceled_at',  Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Allegro Cancel Time')
    ->addColumn('allegro_created_at',   Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Allegro Creation Time')
    ->addColumn('created_at',           Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at',           Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    // Indexes
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid', array('auction_id')),
        array('auction_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid', array('contractor_id')),
        array('contractor_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid', array('allegro_user_id')),
        array('allegro_user_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid', array('allegro_created_at')),
        array('allegro_created_at'))
    // Unique index for pair - allegro user - allegro auction
    // Its a secondary primary key
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid', 
        array('allegro_user_id', 'allegro_auction_id')),
        array('allegro_user_id', 'allegro_auction_id'), 
        array("type"=>Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    // Foreign keys
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction_bid', 'auction_id', 'orbaallegro/auction', 'auction_id'),
        'auction_id', $installer->getTable('orbaallegro/auction'), 'auction_id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction_bid', 'contractor_id', 'orbaallegro/contractor', 'contractor_id'),
        'contractor_id', $installer->getTable('orbaallegro/contractor'), 'contractor_id', 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);


/**
 * Add bid id to transaction auction
 */
$transactionAuctionTable = $installer->getTable('orbaallegro/transaction_auction');
$connection->addColumn($transactionAuctionTable, "bid_id", array(
    "type" => Varien_Db_Ddl_Table::TYPE_INTEGER,
    "nullable" => true,
    "comment" => "Bid id"
));
$connection->addIndex($transactionAuctionTable, 
        $installer->getConnection()->getIndexName($transactionAuctionTable, array("bid_id")), 
        array("bid_id")
);
$connection->addForeignKey(
        $connection->getForeignKeyName($transactionAuctionTable, "bid_id", $auctionBidTable, "bid_id"),
        $transactionAuctionTable, 
        "bid_id", 
        $auctionBidTable, 
        "bid_id", 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, 
        Varien_Db_Ddl_Table::ACTION_CASCADE
);


/**
 * Add bid counts
 */
$auctionTable = $installer->getTable('orbaallegro/auction');
$connection->addColumn($auctionTable, "transaction_items_sold", array(
    "type" => Varien_Db_Ddl_Table::TYPE_INTEGER,
    "nullable" => false,
    "comment" => "Transaction sold items count"
));


/**
 * Add bid id to quote / order item
 */

// Auction id
$bidId = "orbaallegro_bid_id";
$connection
    ->addColumn($installer->getTable('sales/quote_item'), $bidId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$bidId
    ));

$connection->addColumn($installer->getTable('sales/order_item'), $bidId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$bidId
    ));

/**
 * Add contractor id to quote / oreder
 */

$contractorId = "orbaallegro_contractor_id";
$connection
    ->addColumn($installer->getTable('sales/quote'), $contractorId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$contractorId
    ));

$connection->addColumn($installer->getTable('sales/order'), $contractorId, array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'=>$contractorId
    ));

/**
 * Add bid notes
 */

$bidNotesTable = $this->getTable("orbaallegro/auction_bid_note");

$table = $connection
    ->newTable($bidNotesTable)
    ->addColumn("note_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    // Bid data
    ->addColumn('bid_id',               Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false))
    ->addColumn('user_id',              Varien_Db_Ddl_Table::TYPE_INTEGER, null, array())
    ->addColumn('user_name',            Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false))
    ->addColumn('content',              Varien_Db_Ddl_Table::TYPE_TEXT, 1024, array('nullable' => false))
    ->addColumn('created_at',           Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('updated_at',           Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    // Indexes
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid_note', array('bid_id')),
        array('bid_id'))
    ->addIndex($installer->getIdxName('orbaallegro/auction_bid_note', array('user_id')),
        array('user_id'))
    // Foreign keys
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction_bid_note', 'bid_id', 'orbaallegro/auction_bid', 'bid_id'),
        'bid_id', $installer->getTable('orbaallegro/auction_bid'), 'bid_id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('orbaallegro/auction_bid_note', 'user_id', 'admin/user', 'user_id'),
        'user_id', $installer->getTable('admin/user'), 'user_id', 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();