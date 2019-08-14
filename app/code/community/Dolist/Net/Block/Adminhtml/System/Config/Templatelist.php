<?php
/**
 * Dolist block to test connection and retrieve Magento email template list
 * Retrieve Magento template list (original and custom ones)
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Templatelist extends Mage_Core_Block_Html_Select
{
    /**
     * Template list cache
     *
     * @var array
     */
    private $_templateList;

    /**
     * Retrieve Magento template list (original and custom ones)
     *
     * @return array|string Template list
     */
    protected function _getTemplateList()
    {
        if (is_null($this->_templateList)) {
            $this->_templateList = array();
            
            $collection = Mage::getModel('dolist/system_config_source_email_template')->toOptionArray();
            foreach ($collection as $id => $item) {
                $this->_templateList[$id] = $item;
            }
        }
        return $this->_templateList;
    }
    
    /**
     * Set form input name. Mandatory to submit form then store values
     * 
     * @param string $value Form input name
     * 
     * @return Dolist_Net_Block_Adminhtml_System_Config_Templatelist
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string HTML
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getTemplateList() as $id => $template) {
                $value = $template['value'];
                $label = $template['label'];
                // Allow empty first line but remove 'locale default model'
                if ($value != '' || $label == '') {
                    $this->addOption($value, addslashes($label));
                }
            }
        }
        return parent::_toHtml();
    }
    
    /**
     * Return option HTML node
     * Compatibility with old versions
     * 
     * @param array   $option   Option
     * @param boolean $selected Selected
     * 
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }

        $params = '';
        if (!empty($option['params']) && is_array($option['params'])) {
            foreach ($option['params'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $keyMulti => $valueMulti) {
                        $params .= sprintf(' %s="%s" ', $keyMulti, $valueMulti);
                    }
                } else {
                    $params .= sprintf(' %s="%s" ', $key, $value);
                }
            }
        }

        return sprintf(
            '<option value="%s"%s %s>%s</option>',
            $this->htmlEscape($option['value']),
            $selectedHtml,
            $params,
            $this->htmlEscape($option['label'])
        );
    }
    
    /**
     * Calculate option hash 
     * Compatibility with old versions
     * 
     * @param string $optionValue Option value
     * 
     * @return string
     * @see Mage_Core_Block_Html_Select
     */
    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }
}
