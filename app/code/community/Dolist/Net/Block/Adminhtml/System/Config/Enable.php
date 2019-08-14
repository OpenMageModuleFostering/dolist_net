<?php
/**
 * Dolist block to display "Enable" label in Back Office but cannot modify its value
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Enable extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Return Dolist 'Enabled' label to be displayed in Back Office system configuration
     *
     * @param Varien_Data_Form_Element_Abstract $element Element
     * 
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return Mage::helper('adminhtml')->__('Enabled');
    }
}
