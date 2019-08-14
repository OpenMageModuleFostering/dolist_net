<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// Create new table to store Dolist-EMT templates

$installer->run("ALTER TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_calculatedfields') . " DROP PRIMARY KEY;");
$installer->run("ALTER TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_calculatedfields') . " ADD id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;");
$installer->run("ALTER TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_calculatedfields') . " ADD store_id INT AFTER start_date;");
$installer->run("ALTER TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_calculatedfields') . " ADD CONSTRAINT uc_customerid_storeid UNIQUE (customer_id,store_id);");
$installer->run("TRUNCATE TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistv8_calculatedfields') . ";");

$installer->endSetup();
