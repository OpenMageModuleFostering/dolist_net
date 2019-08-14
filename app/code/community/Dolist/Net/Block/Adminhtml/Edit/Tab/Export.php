<?php

class Dolist_Net_Block_Adminhtml_Edit_Tab_Export  extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return 'Export';
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return 'Export';
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

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
     * Retrieve update segments url to Dolist-V8
     *
     * @return string
     */
    public function getUpdateCustomerFieldsUrl()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        return $this->getUrl('*/*/updateCustomFields', array('store' => $storeId));
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