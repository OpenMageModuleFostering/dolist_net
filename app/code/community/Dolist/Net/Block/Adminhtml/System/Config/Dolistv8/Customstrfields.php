<?php
/**
 * Dolist block to display mapping between Magento customer attributes and Dolist-V8 attribute names
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Dolistv8_Customstrfields extends Dolist_Net_Block_Adminhtml_System_Config_Abstract
{
    protected $_magentoCustomerAttributeRenderer;
    protected $_dolistv8AttributeRenderer;
    protected $_limitRows = 30;
    
    /**
     * Prepare to render
     * 
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->setTemplate('dolist/system/config/dolistv8/array.phtml');
        
        $this->addColumn(
            'magento_customer_attribute_1',
            array(
                'label'     => Mage::helper('dolist')->__('Magento customer attribute'),
                'renderer'  => $this->_getMagentoCustomerAttributeRenderer(),
            )
        );
        $this->addColumn(
            'dolist_custom_str_fields',
            array(
                'label'     => Mage::helper('dolist')->__('Dolist-V8 attribute name'),
                'renderer'  => $this->_getDolistv8AttributeRenderer(),
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('dolist')->__('Add Mapping');
    }
    
    /**
     * Renderer for magento email templates
     * 
     * @return void
     */
    protected function _getMagentoCustomerAttributeRenderer()
    {
        if (!$this->_magentoCustomerAttributeRenderer) {
            $this->_magentoCustomerAttributeRenderer = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_customerattributelist',
                '',
                array(
                    'is_render_to_js_template'  => true,
                    'backend_type'              => array('static', 'varchar', 'text')
                )
            );
            
            $this->_magentoCustomerAttributeRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_magentoCustomerAttributeRenderer;
    }
    
    /**
     * Renderer for Dolist-EMT templates
     * 
     * @return void
     */
    protected function _getDolistv8AttributeRenderer()
    {
        if (!$this->_dolistv8AttributeRenderer) {
            $this->_dolistv8AttributeRenderer = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_attributelist',
                '',
                array(
                    'is_render_to_js_template' => true,
                    'custom_field_label'       => 'CustomStr',
                    'custom_field_size'        => 30)
            );
            
            $this->_dolistv8AttributeRenderer->setClass('dolistv8_custom_str_fields_select');
            $this->_dolistv8AttributeRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_dolistv8AttributeRenderer;
    }
    
    /**
     * Prepare existing row data object
     * Select stored values in select lists
     *
     * @param Varien_Object $row Row to select
     * 
     * @return void
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->calcOptionHash(
                $row->getData('magento_customer_attribute_1'),
                $this->_getMagentoCustomerAttributeRenderer()
            ),
            'selected="selected"'
        )->setData(
            'option_extra_attr_' . $this->calcOptionHash(
                $row->getData('dolist_custom_str_fields'),
                $this->_getDolistv8AttributeRenderer()
            ),
            'selected="selected"'
        );
    }
    
    /**
     * Return limit of rows to allow
     * 
     * @return int
     */
    public function getLimitRows()
    {
        return $this->_limitRows;
    }
}