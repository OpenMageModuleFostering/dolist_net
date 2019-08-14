<?php
/**
 * Add table to store queued messages
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @author    Clever Age
 * @copyright Clever Age
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();


$installer->run(
    "
        DROP TABLE IF EXISTS {$this->getTable('dolist_dolistv8_calculatedfields')};
        CREATE TABLE {$this->getTable('dolist_dolistv8_calculatedfields')} (
          `customer_id` int(10) unsigned,
          `first_order_amount` decimal(12,4) DEFAULT NULL,
          `first_order_amount_with_vat` decimal(12,4) DEFAULT NULL,
          `last_order_amount` decimal(12,4) DEFAULT NULL,
          `last_order_amount_with_vat` decimal(12,4) DEFAULT NULL,
          `total_orders_amount` decimal(12,4) DEFAULT NULL,
          `total_orders_amount_with_vat` decimal(12,4) DEFAULT NULL,
          `average_unique_product_count` decimal(6,2) DEFAULT NULL,
          `average_product_count_by_command_line` decimal(6,2) DEFAULT NULL,
          `total_product_count` double DEFAULT NULL,
          `total_orders_count` double DEFAULT NULL,
          `last_unordered_cart_amount` decimal(12,4) DEFAULT NULL,
          `last_unordered_cart_amount_with_vat` decimal(12,4) DEFAULT NULL,
          `discount_rule_count` decimal(6,2) DEFAULT NULL,
          `last_orders_range` double DEFAULT NULL,
          `first_order_date` date DEFAULT NULL,
          `last_order_date` date DEFAULT NULL,
          `last_unordered_cart_date` date DEFAULT NULL,
          `orders_expire` date DEFAULT NULL,
          `cart_expire` date DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL,
          `config` int DEFAULT NULL,
          `start_date` date DEFAULT NULL,

          PRIMARY KEY  (`customer_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='dolistemt message queue';
        "
);

$installer->endSetup();
