<?php
/**
 * Create table structures in database
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @author    Clever Age
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// Create new table to store Dolist-EMT templates
$installer->run(
    "
    DROP TABLE IF EXISTS " . Mage::helper('dolist')->getTablename('dolist_reports') . ";
    CREATE TABLE " . Mage::helper('dolist')->getTablename('dolist_reports') . " (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `type` varchar(255) NOT NULL,
      `name` varchar(255) NOT NULL,
      `started_at` DATETIME DEFAULT NULL,
      `ended_at` DATETIME DEFAULT NULL,
      `progress_current` int(10) DEFAULT NULL,
      `progress_end` int(10) DEFAULT NULL,
      `logs` LONGTEXT NOT NULL,
      `last_logs` varchar(255) NOT NULL,
      `result` varchar(255) DEFAULT NULL,
      `updated_at` DATETIME NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='dolist_reports';
    "
);

$installer->endSetup();
