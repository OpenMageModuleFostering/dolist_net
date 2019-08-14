<?php
/**
 * Dolist-V8 block to export customers from new Back Office menu
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_Customerexport extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve full export url to Dolist-V8
     * 
     * @return string
     */
    public function getFullExportUrl()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        return $this->getUrl('*/*/fullExport', array('store' => $storeId));
    }

    /**
     * Retrieve differential export url to Dolist-V8
     * 
     * @return string
     */
    public function getDifferentialExportUrl()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        return $this->getUrl('*/*/differentialExport', array('store' => $storeId));
    }
    
    /**
     * Retrieve update segments url to Dolist-V8
     * 
     * @return string
     */
    public function getUpdateSegmentsUrl()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        return $this->getUrl('*/*/updateSegments', array('store' => $storeId));
    }

    /**
     * Check if customer segment can be used, ie if this feature is available for current platform
     * 
     * @return boolean
     */
    public function showCustomerSegment()
    {
        return Mage::helper('dolist')->isCustomerSegmentEnabled();
    }
}
