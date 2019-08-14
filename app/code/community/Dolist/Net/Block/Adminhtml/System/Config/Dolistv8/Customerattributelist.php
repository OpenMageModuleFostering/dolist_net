<?php
/**
 * Dolist block to display customer attribute list which can be exported to Dolist-V8
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Dolistv8_Customerattributelist extends Mage_Core_Block_Html_Select
{
    /**
     * Template list cache
     *
     * @var array
     */
    private $_dolistv8AttributeList;

    /**
     * Retrieve options list
     *
     * @return array|string List
     */
    protected function _getList()
    {
        if (is_null($this->_dolistv8AttributeList)) {
            $this->_dolistv8AttributeList = array();
            
            $collection = Mage::getModel('dolist/system_config_source_dolistv8_customerattributelist')->toOptionArray(
                $this->getBackendType()
            );
            foreach ($collection as $id => $item) {
                $this->_dolistv8AttributeList[$id] = $item;
            }
        }
        return $this->_dolistv8AttributeList;
    }
    
    /**
     * Set form input name. Mandatory to submit form then store values
     * 
     * @param string $value Form input name
     * 
     * @return Dolist_Net_Block_Adminhtml_System_Config_Dolistv8_Customerattributelist
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
            
            foreach ($this->_getList() as $id => $template) {
                
                if (!is_array($template['value'])) {
                    $value = $template['value'];
                    $label = $template['label'];

                    // if label empty, display value
                    if ($label == '') {
                        $label = $value;
                    }

                    $this->addOption($value, addslashes($label));
                } else {
                    $this->addOption($template['value'], addslashes($template['label']));
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
            $selectedHtml .= ' #{option_extra_attr_' . $option['value'] . '}';
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
