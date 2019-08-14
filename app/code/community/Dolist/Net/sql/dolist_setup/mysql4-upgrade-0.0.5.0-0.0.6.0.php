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
    "ALTER TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_customfields') . " ADD scope VARCHAR(8) AFTER magento_field, ADD scope_id INT AFTER scope;"
);
$installer->run(
    "ALTER TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_customfields') . " ADD CONSTRAINT uc_namescope UNIQUE (name,scope_id)"
);

$installer->endSetup();
