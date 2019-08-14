<?php
/**
 * UPdate newsletter_subscriber table structure in database
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
        ALTER TABLE {$this->getTable('newsletter_subscriber')}
        DROP COLUMN `updated_at`,
        ADD COLUMN `updated_at` int(10) DEFAULT 0;
        "
    );
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
