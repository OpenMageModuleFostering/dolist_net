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
    DROP TABLE IF EXISTS {$this->getTable('dolist_dolistv8_customfields')};
    CREATE TABLE {$this->getTable('dolist_dolistv8_customfields')} (
      `id` int(10) unsigned AUTO_INCREMENT,
      `type` varchar(255) NOT NULL,
      `name` varchar(255) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      `display` varchar(255) DEFAULT NULL,
      `translationKey` varchar(255) DEFAULT NULL,
      `displayRank` int(10) NOT NULL,
      `isCustom` tinyint(1) NOT NULL DEFAULT 0,
      `magento_field` varchar(255) DEFAULT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='dolistv8_customfields';
    "
);

$installer->endSetup();
