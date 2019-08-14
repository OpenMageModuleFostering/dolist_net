<?php
/**
 * Dolist source chooser to select Dolist-EMT templates
 * Retrieve stored values
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_System_Config_Source_Dolistemt_Template
{
    /**
     * Return list of Dolist-EMT templates stored in Magento when "Test Dolist-EMT connection" button is clicked on
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storedArray   = Mage::getModel('dolist/dolistemt_template')->getCollection()->toArray();
        $options = array();
        
        if ($storedArray['totalRecords'] > 0) {
            foreach ($storedArray['items'] as $storedTemplate) {
                $options[] = array(
                    'value' => $storedTemplate['template_id'],
                    'label' => $storedTemplate['template_name']
                );
            }
        }

        $options[] = array(
            'value' => -1,
            'label' => Mage::helper('dolist')->__('Use magento native email system')
        );
        
        return $options;
    }
}
