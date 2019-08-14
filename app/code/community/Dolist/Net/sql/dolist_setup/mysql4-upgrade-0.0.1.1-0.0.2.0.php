<?php
/**
 * Add table to store queued messages
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();
    
// Change updated_at field format to timestamp to avoid timezone conflicts
try {
    // Alter column only if column does not already exist
    $installer->run(
        "
        DROP TABLE IF EXISTS {$this->getTable('dolist_dolistemt_message_queue')};
        CREATE TABLE {$this->getTable('dolist_dolistemt_message_queue')} (
          `id` bigint unsigned NOT NULL auto_increment,
          `template_id` int(10) unsigned NOT NULL,
          `serialized_message` LONGTEXT NOT NULL default '',
          `store_id` smallint(5) unsigned  NOT NULL default 0,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='dolistemt message queue';
        "
    );
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
