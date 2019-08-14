<?php
/**
 * Dolist block to display mapping between Magento and Dolist-EMT template list
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Dolistemt_Templatemapping extends Dolist_Net_Block_Adminhtml_System_Config_Abstract
{
    /**
     * @var Dolist_Net_Block_Adminhtml_System_Config_Templatelist
     */
    protected $_magentoTemplateRenderer;
    
    /**
     * @var Dolist_Net_Block_Adminhtml_System_Config_Templatelist
     */
    protected $_dolistemtTemplateRenderer;
    
    /**
     * Prepare to render
     * 
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'magento_template',
            array(
                'label'     => Mage::helper('dolist')->__('Magento template'),
                'renderer'  => $this->_getMagentoTemplateRenderer(),
            )
        );
        $this->addColumn(
            'dolist_template',
            array(
                'label'     => Mage::helper('dolist')->__('Dolist-EMT template'),
                'renderer'  => $this->_getDolistemtTemplateRenderer(),
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
    protected function _getMagentoTemplateRenderer()
    {
        if (!$this->_magentoTemplateRenderer) {
            $this->_magentoTemplateRenderer = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_templatelist',
                '',
                array('is_render_to_js_template' => true)
            );
            
            $this->_magentoTemplateRenderer->setClass('magento_email_template_select');
            $this->_magentoTemplateRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_magentoTemplateRenderer;
    }
    
    /**
     * Renderer for Dolist-EMT templates
     * 
     * @return void
     */
    protected function _getDolistemtTemplateRenderer()
    {
        if (!$this->_dolistemtTemplateRenderer) {
            $this->_dolistemtTemplateRenderer = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistemt_templatelist',
                '',
                array('is_render_to_js_template' => true)
            );
            
            $this->_dolistemtTemplateRenderer->setClass('dolistemt_email_template_select');
            $this->_dolistemtTemplateRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_dolistemtTemplateRenderer;
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
            'option_extra_attr_' . $this->calcOptionHash($row->getData('magento_template'), $this->_getMagentoTemplateRenderer()),
            'selected="selected"'
        )->setData(
            'option_extra_attr_' . $this->calcOptionHash($row->getData('dolist_template'), $this->_getDolistemtTemplateRenderer()),
            'selected="selected"'
        );
    }
}