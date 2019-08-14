<?php
/**
 * Dolist abstract block to back-office fields
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Abstract extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Indication whether block is prepared to render or no
     *
     * @var bool
     */
    protected $_isPreparedToRender = false;
    
    /**
     * Check if columns are defined, set template
     * Compatibility with old versions
     * 
     * @return void
     */
    public function __construct()
    {
        if (!$this->_addButtonLabel) {
            $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        }
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/form/field/array.phtml');
        }
    }
    
    /**
     * Render block HTML
     * Compatibility with old versions
     * 
     * @return string Html content
     * @throws Exception
     */
    protected function _toHtml()
    {
        if (!$this->_isPreparedToRender) {
            $this->_prepareToRender();
            $this->_isPreparedToRender = true;
        }
        if (empty($this->_columns)) {
            throw new Exception('At least one column must be defined.');
        }
        return parent::_toHtml();
    }
    
    /**
     * Obtain existing data from form element
     * Each row will be instance of Varien_Object
     * Compatibility with old versions
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = array();
        /** @var Varien_Data_Form_Element_Abstract */
        $element = $this->getElement();
        if ($element->getValue() && is_array($element->getValue())) {
            foreach ($element->getValue() as $rowId => $row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->htmlEscape($value);
                }
                $row['_id'] = $rowId;
                $result[$rowId] = new Varien_Object($row);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }
        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }
    
    /**
     * Calculate option hash 
     * Compatibility with old versions
     * 
     * @param string                   $optionValue Option value
     * @param Mage_Core_Block_Abstract $block       Block
     * 
     * @return string
     * @see Mage_Core_Block_Html_Select
     */
    public function calcOptionHash($optionValue, $block)
    {
        return sprintf('%u', crc32($block->getName() . $block->getId() . $optionValue));
    }
}