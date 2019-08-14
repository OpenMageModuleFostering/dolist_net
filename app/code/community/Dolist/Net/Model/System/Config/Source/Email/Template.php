<?php
/**
 * Dolist source chooser to select all email templates
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_System_Config_Source_Email_Template
{
    /**
     * Return merged array containing original Magento email templates and custom ones (defined in back office)
     *
     * @return array
     */
    public function toOptionArray()
    {
        $originalTemplates = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
        $customTemplates = Mage::getSingleton('adminhtml/system_config_source_email_template')->toOptionArray();

        $options = array_merge($originalTemplates, $customTemplates);
        return $options;
    }
}
