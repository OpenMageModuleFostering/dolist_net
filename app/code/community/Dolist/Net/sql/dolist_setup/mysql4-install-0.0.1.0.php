<?php
/**
 * Create table structures in database
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

// Create new table to store Dolist-EMT templates
$installer->run(
    "
    DROP TABLE IF EXISTS " . Mage::helper('dolist')->getTablename('dolist_dolistemt_template') . ";
    CREATE TABLE " . Mage::helper('dolist')->getTablename('dolist_dolistemt_template') . " (
      `id` int(10) unsigned NOT NULL auto_increment,
      `template_id` int(10) unsigned NOT NULL,
      `template_name` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='dolistemt templates';
    "
);
    
// Add updated_at field in newsletter_subscriber table
try {
    // Alter column only if column does not already exist
    $installer->run(
        "
        ALTER TABLE " . Mage::helper('dolist')->getTablename('newsletter_subscriber') . "
        ADD COLUMN `dolist_status` int(11) NOT NULL default '0',
        ADD COLUMN `updated_at` datetime DEFAULT NULL;
        "
    );
} catch (Exception $e) {
    Mage::logException($e);
}

// Add dolist_code field in directory_country table
try {
    // Alter column only if column does not already exist
    $installer->run(
        "
        ALTER TABLE " . Mage::helper('dolist')->getTablename('directory_country') . "
        ADD COLUMN `dolist_code` varchar(3) NOT NULL default '999';
        "
    );    
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '43' WHERE `country_id` = 'AD';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '645' WHERE `country_id` = 'AE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '647' WHERE `country_id` = 'AE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '660' WHERE `country_id` = 'AF';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '459' WHERE `country_id` = 'AG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '446' WHERE `country_id` = 'AI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '70' WHERE `country_id` = 'AL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '77' WHERE `country_id` = 'AM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '478' WHERE `country_id` = 'AN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '330' WHERE `country_id` = 'AO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '528' WHERE `country_id` = 'AR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '38' WHERE `country_id` = 'AT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '800' WHERE `country_id` = 'AU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '474' WHERE `country_id` = 'AW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '78' WHERE `country_id` = 'AZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '93' WHERE `country_id` = 'BA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '469' WHERE `country_id` = 'BB';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '666' WHERE `country_id` = 'BD';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '2' WHERE `country_id` = 'BE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '236' WHERE `country_id` = 'BF';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '68' WHERE `country_id` = 'BG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '640' WHERE `country_id` = 'BH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '328' WHERE `country_id` = 'BI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '284' WHERE `country_id` = 'BJ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '413' WHERE `country_id` = 'BM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '703' WHERE `country_id` = 'BN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '516' WHERE `country_id` = 'BO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '508' WHERE `country_id` = 'BR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '453' WHERE `country_id` = 'BS';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '675' WHERE `country_id` = 'BT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '391' WHERE `country_id` = 'BW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '73' WHERE `country_id` = 'BY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '421' WHERE `country_id` = 'BZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '404' WHERE `country_id` = 'CA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '306' WHERE `country_id` = 'CF';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '318' WHERE `country_id` = 'CG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '36' WHERE `country_id` = 'CH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '272' WHERE `country_id` = 'CI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '512' WHERE `country_id` = 'CL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '302' WHERE `country_id` = 'CM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '720' WHERE `country_id` = 'CN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '480' WHERE `country_id` = 'CO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '436' WHERE `country_id` = 'CR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '448' WHERE `country_id` = 'CU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '247' WHERE `country_id` = 'CV';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '600' WHERE `country_id` = 'CY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '61' WHERE `country_id` = 'CZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '4' WHERE `country_id` = 'DE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '338' WHERE `country_id` = 'DJ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '8' WHERE `country_id` = 'DK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '460' WHERE `country_id` = 'DM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '456' WHERE `country_id` = 'DO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '208' WHERE `country_id` = 'DZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '22' WHERE `country_id` = 'EA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '500' WHERE `country_id` = 'EC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '53' WHERE `country_id` = 'EE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '220' WHERE `country_id` = 'EG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '336' WHERE `country_id` = 'ER';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '11' WHERE `country_id` = 'ES';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '334' WHERE `country_id` = 'ET';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '32' WHERE `country_id` = 'FI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '815' WHERE `country_id` = 'FJ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '529' WHERE `country_id` = 'FK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '823' WHERE `country_id` = 'FM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '25' WHERE `country_id` = 'FO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '1' WHERE `country_id` = 'FR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '314' WHERE `country_id` = 'GA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '6' WHERE `country_id` = 'GB';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '473' WHERE `country_id` = 'GD';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '76' WHERE `country_id` = 'GE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '496' WHERE `country_id` = 'GF';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '276' WHERE `country_id` = 'GH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '44' WHERE `country_id` = 'GI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '406' WHERE `country_id` = 'GL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '252' WHERE `country_id` = 'GM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '260' WHERE `country_id` = 'GN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '458' WHERE `country_id` = 'GP';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '310' WHERE `country_id` = 'GQ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '9' WHERE `country_id` = 'GR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '416' WHERE `country_id` = 'GT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '257' WHERE `country_id` = 'GW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '488' WHERE `country_id` = 'GY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '740' WHERE `country_id` = 'HK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '424' WHERE `country_id` = 'HN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '92' WHERE `country_id` = 'HR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '452' WHERE `country_id` = 'HT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '64' WHERE `country_id` = 'HU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '21' WHERE `country_id` = 'IC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '700' WHERE `country_id` = 'ID';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '7' WHERE `country_id` = 'IE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '624' WHERE `country_id` = 'IL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '664' WHERE `country_id` = 'IN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '357' WHERE `country_id` = 'IO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '612' WHERE `country_id` = 'IQ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '616' WHERE `country_id` = 'IR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '24' WHERE `country_id` = 'IS';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '5' WHERE `country_id` = 'IT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '464' WHERE `country_id` = 'JM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '628' WHERE `country_id` = 'JO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '732' WHERE `country_id` = 'JP';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '346' WHERE `country_id` = 'KE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '83' WHERE `country_id` = 'KG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '696' WHERE `country_id` = 'KH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '812' WHERE `country_id` = 'KI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '375' WHERE `country_id` = 'KM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '449' WHERE `country_id` = 'KN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '724' WHERE `country_id` = 'KP';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '728' WHERE `country_id` = 'KR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '636' WHERE `country_id` = 'KW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '463' WHERE `country_id` = 'KY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '79' WHERE `country_id` = 'KZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '684' WHERE `country_id` = 'LA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '604' WHERE `country_id` = 'LB';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '465' WHERE `country_id` = 'LC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '37' WHERE `country_id` = 'LI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '669' WHERE `country_id` = 'LK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '268' WHERE `country_id` = 'LR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '395' WHERE `country_id` = 'LS';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '55' WHERE `country_id` = 'LT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '23' WHERE `country_id` = 'LU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '54' WHERE `country_id` = 'LV';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '216' WHERE `country_id` = 'LY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '204' WHERE `country_id` = 'MA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '825' WHERE `country_id` = 'MC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '74' WHERE `country_id` = 'MD';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '370' WHERE `country_id` = 'MG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '824' WHERE `country_id` = 'MH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '96' WHERE `country_id` = 'MK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '232' WHERE `country_id` = 'ML';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '676' WHERE `country_id` = 'MM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '716' WHERE `country_id` = 'MN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '743' WHERE `country_id` = 'MO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '462' WHERE `country_id` = 'MQ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '228' WHERE `country_id` = 'MR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '46' WHERE `country_id` = 'MT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '373' WHERE `country_id` = 'MU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '667' WHERE `country_id` = 'MV';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '386' WHERE `country_id` = 'MW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '412' WHERE `country_id` = 'MX';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '701' WHERE `country_id` = 'MY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '366' WHERE `country_id` = 'MZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '389' WHERE `country_id` = 'NA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '809' WHERE `country_id` = 'NC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '240' WHERE `country_id` = 'NE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '288' WHERE `country_id` = 'NG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '432' WHERE `country_id` = 'NI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '3' WHERE `country_id` = 'NL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '28' WHERE `country_id` = 'NO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '672' WHERE `country_id` = 'NP';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '803' WHERE `country_id` = 'NR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '804' WHERE `country_id` = 'NZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '649' WHERE `country_id` = 'OM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '442' WHERE `country_id` = 'PA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '504' WHERE `country_id` = 'PE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '822' WHERE `country_id` = 'PF';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '801' WHERE `country_id` = 'PG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '708' WHERE `country_id` = 'PH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '662' WHERE `country_id` = 'PK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '60' WHERE `country_id` = 'PL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '408' WHERE `country_id` = 'PM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '813' WHERE `country_id` = 'PN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '455' WHERE `country_id` = 'PR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '10' WHERE `country_id` = 'PT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '520' WHERE `country_id` = 'PY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '644' WHERE `country_id` = 'QA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '372' WHERE `country_id` = 'RE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '66' WHERE `country_id` = 'RO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '94' WHERE `country_id` = 'RS';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '75' WHERE `country_id` = 'RU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '324' WHERE `country_id` = 'RW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '632' WHERE `country_id` = 'SA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '806' WHERE `country_id` = 'SB';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '355' WHERE `country_id` = 'SC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '224' WHERE `country_id` = 'SD';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '30' WHERE `country_id` = 'SE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '706' WHERE `country_id` = 'SG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '329' WHERE `country_id` = 'SH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '91' WHERE `country_id` = 'SI';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '63' WHERE `country_id` = 'SK';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '264' WHERE `country_id` = 'SL';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '248' WHERE `country_id` = 'SN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '342' WHERE `country_id` = 'SO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '492' WHERE `country_id` = 'SR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '311' WHERE `country_id` = 'ST';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '428' WHERE `country_id` = 'SV';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '608' WHERE `country_id` = 'SY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '393' WHERE `country_id` = 'SZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '454' WHERE `country_id` = 'TC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '244' WHERE `country_id` = 'TD';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '280' WHERE `country_id` = 'TG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '680' WHERE `country_id` = 'TH';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '82' WHERE `country_id` = 'TJ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '80' WHERE `country_id` = 'TM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '212' WHERE `country_id` = 'TN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '817' WHERE `country_id` = 'TO';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '52' WHERE `country_id` = 'TR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '472' WHERE `country_id` = 'TT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '807' WHERE `country_id` = 'TV';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '736' WHERE `country_id` = 'TW';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '352' WHERE `country_id` = 'TZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '72' WHERE `country_id` = 'UA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '350' WHERE `country_id` = 'UG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '400' WHERE `country_id` = 'US';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '524' WHERE `country_id` = 'UY';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '81' WHERE `country_id` = 'UZ';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '45' WHERE `country_id` = 'VA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '467' WHERE `country_id` = 'VC';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '484' WHERE `country_id` = 'VE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '461' WHERE `country_id` = 'VG';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '690' WHERE `country_id` = 'VN';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '816' WHERE `country_id` = 'VU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '811' WHERE `country_id` = 'WF';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '819' WHERE `country_id` = 'WS';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '653' WHERE `country_id` = 'YE';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '377' WHERE `country_id` = 'YT';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '48' WHERE `country_id` = 'YU';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '388' WHERE `country_id` = 'ZA';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '378' WHERE `country_id` = 'ZM';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '322' WHERE `country_id` = 'ZR';");
    $installer->run("UPDATE " . Mage::helper('dolist')->getTablename('directory_country') . " SET `dolist_code` = '382' WHERE `country_id` = 'ZW';");
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
