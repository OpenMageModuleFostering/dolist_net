<?php
/**
 * Dolist block to test connection and retrieve Dolist-EMT template list
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Dolistemt_Testconnection
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Set template to itself
     * 
     * @return Dolist_Net_Block_Adminhtml_System_Config_Dolistemt_Testconnection
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('dolist/system/config/dolistemt/testconnection.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element Element
     * 
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element Element
     * 
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $website = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
        if ($website->getId() == 0) {
            $storeId = Mage::app()->getDefaultStoreView()->getId();
        } else {
            $storeId = $website->getDefaultStore()->getId();
        }
        
        $this->addData(
            array(
                'button_label'  => Mage::helper('dolist')->__('Test Dolist-EMT connection'),
                'html_id'       => $element->getHtmlId(),
                'ajax_url'      => Mage::getSingleton('adminhtml/url')->getUrl('*/system_config_testconnection/pingdolistemt'),
                'store_id'      => $storeId
            )
        );

        return $this->_toHtml();
    }
}