<?php
/**
 * Dolist-V8 admin observer
 * Used to add button in Back Office without massive rewrites
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Adminhtml_Observer
{
     /**
     * Add "export this segment" button in admin/customersegment/edit page
     * 
     * @param Varien_Event $observer Observer
     * 
     * @return Dolist_Net_Model_Adminhtml_Observer
     */
    public function addCustomerSegmentExportButton($observer)
    {
        $segment = $observer->getEvent()->getSegment();
        if ($segment && $segment->getId()) {
        
            $segmentWebsiteIds = $segment->getWebsiteIds();
            // Replace segment website ids with segment website default store view ids
            $segmentStoreIds = array();
            foreach ($segmentWebsiteIds as $segmentWebsiteId) {
                $website = Mage::app()->getWebsite($segmentWebsiteId);
                $segmentStoreIds[$segmentWebsiteId] = $website->getDefaultStore()->getId();
            }

            $enabledStoreIds = array();
            foreach ($segmentStoreIds as $segmentWebsiteId => $segmentStoreId) {
                if ($this->_getHelper()->isDolistV8Enabled($segmentStoreId)) {
                    $enabledStoreIds[$segmentWebsiteId] = $segmentStoreId;
                }
            }

            // If this segment is Dolist-V8 enabled for at least one store from its scope
            if (!empty($enabledStoreIds)) {
                $block = $observer->getEvent()->getBlock();

                $url               = '*/customerdolist/exportSegment';
                $removeUrl         = '*/customerdolist/removeSegment';
                $urlAddToCron      = '*/customerdolist/addSegmentExportToCron';
                $urlRemoveFromCron = '*/customerdolist/removeSegmentExportFromCron';


                $urlParams = array(
                    'segment_id'  => $segment->getId(),
                    'website_ids' => serialize(array_keys($enabledStoreIds)), // Dolist-V8 enabled websites
                    'store_ids'   => serialize($enabledStoreIds),
                );

                //Get Segment Export Cron Status to build the correct action button
                if(!$this->_getHelper()->isSegmentExportCronEnabled($segment->getId())) {
                   $button_export_cron = array(
                    'class'     => 'add',
                    'label'     => Mage::helper('dolist')->__('Enable export from Cron'),
                    'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl($urlAddToCron, $urlParams) . '\')'
                    );
                } else {
                    $button_export_cron = array(
                    'class'     => 'delete',
                    'label'     => Mage::helper('dolist')->__('Disable export from Cron'),
                    'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl($urlRemoveFromCron, $urlParams) . '\')'
                    );
                }

                // Newline
                $block->addButton(
                    'dolistv8_newline',
                    array(
                        'class'     => '',
                        'label'     => '',
                        'onclick'   => '',
                        'style'     => 'display:block; visibility: hidden;',
                    ),
                    3
                );

                $block->addButton('toggle_export_cron',
                    $button_export_cron,
                    4);


                // If this segment is not already exported
                if (!$this->_getHelper()->isExportedSegment($segment->getId(), $enabledStoreIds)) {

                    $block->addButton(
                        'dolistv8_export',
                        array(
                            'class'     => 'add',
                            'label'     => Mage::helper('dolist')->__('Export this segment to Dolist-V8'),
                            'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl($url, $urlParams) . '\')',
                        ),
                        5
                    );
                } else {

                    // Else, propose to export it again (just one segment export)
                    $block->addButton(
                        'dolistv8_export',
                        array(
                            'class'     => 'add',
                            'label'     => Mage::helper('dolist')->__('Export again this segment to Dolist-V8'),
                            'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl($url, $urlParams) . '\')',
                        ),
                        5
                    );

                    // Add new button to remove this segment from export list dolist_exported_segment_list
                    $block->addButton(
                        'dolistv8_remove_export',
                        array(
                            'class' => 'add',
                            'label' => Mage::helper('dolist')->__('Remove this segment from exported list'),
                            'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl($removeUrl, $urlParams) . '\')',
                        ),
                        6
                    );

                }
            }
        }
        return $this;
    }
    
    /**
     * Add warning in back-office when customer edition if this customer gets invalid dolist_status
     * in newsletter_subscriber table
     * 
     * @param Varien_Event $observer Observer
     * 
     * @return Dolist_Net_Model_Adminhtml_Observer
     */
    public function warnCustomerDolistStatus($observer)
    {
        $customerId = $observer->getEvent()->getCustomerId();
        if ($customerId) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($customerId, 'customer_id');
            
            if ($subscriber->getId()) {
                $errorMessage = $this->_getHelper()->getDolistStatusErrorMessage($subscriber->getDolistStatus(), 'back');
                
                if (!is_null($errorMessage)) {
                    Mage::getSingleton('adminhtml/session')->addNotice($this->_getHelper()->__($errorMessage));
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Clean up exported segment list
     * While saving customer segment, check if current segment must be removed from exported segment list
     * 
     * @param Varien_Event $observer Observer
     * 
     * @return Dolist_Net_Model_Adminhtml_Observer
     */
    public function checkCustomerSegmentSave($observer)
    {
        $segment = $observer->getEvent()->getObject();
        $allStoreIds = array_keys(Mage::app()->getStores());
        $segmentStoreIds = array();
        foreach ($segment->getWebsiteIds() as $websiteId) {
            $segmentStoreIds[] = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
        }
        
        // Remove exported segment from stores where segment is not applied to
        foreach (array_diff($allStoreIds, $segmentStoreIds) as $storeId) {
            $this->_getHelper()->removeExportedSegment($segment->getId(), $storeId);
        }
        
        return $this;
    }
    
    /**
     * Retrieve model helper
     *
     * @return Dolist_Net_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('dolist');
    }
    
    /**
     * remove all dolist Flags (filenames, last exports, etc.) if the section params has changed
     * 
     * @param Varien_Event $observer
     * 
     * @return Dolist_Net_Model_Adminhtml_Observer
     */
    public function removeDolistFlags($observer)
    {
        // DIRTY but the flag has no collection
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write');
        /* @var $write Varien_Db_Adapter_Pdo_Mysql */
        $tablename = $resource->getTableName('core_flag');
        $write->query('delete from ' . $tablename . ' where flag_code like "dolist%"');
        
        return $this;
    }
}
