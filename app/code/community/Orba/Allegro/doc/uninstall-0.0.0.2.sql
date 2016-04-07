ALTER TABLE `sales_flat_quote`
  DROP `orbaallegro_transaction_id`,
  DROP `orbaallegro_shipment_id`,
  DROP `orbaallegro_payment_id`,
  DROP `orbaallegro_allegro_buyer_id`,
  DROP `orbaallegro_country_code`;

ALTER TABLE `sales_flat_quote_address`
  DROP `orbaallegro_address_id`,
  DROP `orbaallegro_allegro_buyer_id`,
  DROP `orbaallegro_shipment_id`;

ALTER TABLE  `sales_flat_quote_item`
  DROP  `orbaallegro_auction_id` ;

ALTER TABLE `sales_flat_order`
  DROP `orbaallegro_transaction_id`,
  DROP `orbaallegro_shipment_id`,
  DROP `orbaallegro_payment_id`,
  DROP `orbaallegro_allegro_buyer_id`,
  DROP `orbaallegro_country_code`;

ALTER TABLE `sales_flat_order_address`
  DROP `orbaallegro_address_id`,
  DROP `orbaallegro_allegro_buyer_id`,
  DROP `orbaallegro_shipment_id`;

ALTER TABLE  `sales_flat_order_item`
  DROP  `orbaallegro_auction_id` ;


DROP TABLE `orba_allegro_transaction_address`;
DROP TABLE `orba_allegro_transaction_auction`;
DROP TABLE `orba_allegro_transaction_serialized`;
DROP TABLE `orba_allegro_transaction`;
DROP TABLE `orba_allegro_contractor`;
DROP TABLE `orba_allegro_auction_serialized`;
DROP TABLE `orba_allegro_auction`;
DROP TABLE `orba_allegro_pickpoint`;
DROP TABLE `orba_allegro_pickpoint_provider`;
DROP TABLE `orba_allegro_category`;
DROP TABLE `orba_allegro_shop_category`;
DROP TABLE `orba_allegro_form_options`;
DROP TABLE `orba_allegro_eav_attribute`;
DROP TABLE `orba_allegro_template_datetime`, `orba_allegro_template_decimal`, `orba_allegro_template_int`, `orba_allegro_template_text`, `orba_allegro_template_varchar`;
DROP TABLE `orba_allegro_template`;
DROP TABLE `orba_allegro_mapping_store`;
DROP TABLE `orba_allegro_mapping_shipment`;
DROP TABLE `orba_allegro_mapping_payment`;
DROP TABLE `orba_allegro_mapping_sellform`;
DROP TABLE `orba_allegro_mapping`;
DROP TABLE `orba_allegro_service`;

DELETE FROM `cms_block` WHERE identifier LIKE "orbaallegro_%";
DELETE FROM `eav_attribute` WHERE `entity_type_id` = (SELECT `entity_type_id` FROM `eav_entity_type` WHERE `entity_type_code`="orbaallegro_template");
DELETE FROM `eav_entity_type` WHERE `entity_type_code`="orbaallegro_template";
DELETE FROM `core_resource` WHERE `code`="orbaallegro_setup";
DELETE FROM `core_config_data` WHERE `path` LIKE "orbaallegro%";
DELETE FROM `eav_attribute` WHERE attribute_code IN ('orbaallegro_use_mapping', 'orbaallegro_category_id', 'orbaallegro_template_id', 'orbaallegro_shop_category_id', 'orbaallegro_youtube_code');

/*
rm -rf app/code/community/Orba/Allegro/
rm -rf app/design/adminhtml/default/default/layout/orbaallegro.xml
rm -rf app/design/adminhtml/default/default/template/orbaallegro/
rm -rf app/design/frontend/base/default/template/orbaallegro/
rm -rf app/etc/modules/Orba_Allegro.xml
rm -rf app/locale/pl_PL/Orba_Allegro.csv
rm -rf js/orbaallegro/
rm -rf skin/adminhtml/default/default/orbaallegro/
rm -rf skin/frontend/base/default/orbaallegro/

*/