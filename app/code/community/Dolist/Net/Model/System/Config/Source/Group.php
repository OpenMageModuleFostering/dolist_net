<?php
/**
 * Dolist source chooser to select custom str field for group
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_System_Config_Source_Group
{
    /**
     * Return Dolist-V8 fields able to contain Magento customer group
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getModel('dolist/system_config_source_dolistv8_customfield')->toOptionArray('CustomStr', 30);
    }
}
