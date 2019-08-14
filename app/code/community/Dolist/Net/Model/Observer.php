<?php
/**
 * Dolist Observer
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Observer extends Mage_Core_Model_Abstract
{
    const SEND_MESSAGE_PROCESS_ID = 'dolist_send_message';
    
    protected $_processes = array();

    /**
     * Update calculatedfields table on new order
     *
     * @param Varien_Event_Observer $event
     */
    public function onNewOrder(Varien_Event_Observer $event)
    {
        $currentOrder = null;
        /** @var Mage_Sales_Model_Order $currentOrder */
        if ($event) {
            $currentOrder = $event->getData('order');
        }

        if ($currentOrder) {
            $customerId = $currentOrder->getData('customer_id');

            if (!$customerId) {
                return;
            }

            /** @var Dolist_Net_Model_Dolistv8_Calculatedfields $calculatedFields */
            $calculatedFields = Mage::getModel('dolist/dolistv8_calculatedfields');
            $calculatedFields->setStoreId($currentOrder->getStoreId())->loadByCustomerId($customerId);

            $dates = $calculatedFields->computeOrderDataTtl();
            $now = new DateTime('now');

            // full update
            if (!$calculatedFields->getId()) {
                $calculatedFields->compute();
            } else {
                if ((in_array(Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_mode'), array(Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_1, Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_3, Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_6, Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_12, Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_24)) && $calculatedFields->getData('orders_expire') == null) || ($calculatedFields->getData('orders_expire') != null && strtotime($calculatedFields->getData('orders_expire')) < $now->getTimestamp())) {
                    $calculatedFields->compute();
                } else {
                    $calculatedFields->addData(array(
                        "total_orders_amount" => $calculatedFields->getData('total_orders_amount') + ($currentOrder->getData('grand_total') - $currentOrder->getData('tax_amount')),
                        "total_orders_amount_with_vat" => $calculatedFields->getData('total_orders_amount') + $currentOrder->getData('grand_total'),
                        "average_unique_product_count" => $calculatedFields->computeAverageUniqueProductCount($dates),
                        "average_product_count_by_command_line" => $calculatedFields->computeAverageProductCountByCommandLine($dates),
                        "total_product_count" => $calculatedFields->getData('total_product_count') + $currentOrder->getData('total_item_count'),
                        "total_orders_count" => $calculatedFields->getData('total_orders_count') + 1,
                        "discount_rule_count" => $calculatedFields->computeDiscountRuleCount(),
                    ));
                }

                if (!$calculatedFields->getData('first_order_amount') || $calculatedFields->getData('first_order_amount') == 0) {
                    $calculatedFields->setData('first_order_amount', $calculatedFields->computeFirstOrderAmount());
                }
                if (!$calculatedFields->getData('first_order_date')) {
                    $calculatedFields->setData('first_order_date', $calculatedFields->getFirstOrderDate());
                }

                if (!$calculatedFields->getData('first_order_amount_with_vat')) {
                    $calculatedFields->setData('first_order_amount_with_vat', $calculatedFields->computeFirstOrderAmount(true));
                }

                $calculatedFields->setData('last_order_amount', $calculatedFields->computeLastOrderAmount());
                $calculatedFields->setData('last_order_amount_with_vat', $calculatedFields->computeLastOrderAmount(true));
                $calculatedFields->setData('last_order_date', $calculatedFields->getLastOrderDate());
                $calculatedFields->setData('last_orders_range', (strtotime($currentOrder->getData('created_at')) - strtotime($calculatedFields->getData('last_order_date'))) / 60 / 60 / 24);
                $calculatedFields->setData('updated_at', $now->format('Y-m-d H:i:s'));

                if (is_array($dates) && !empty($dates) && array_key_exists('stop', $dates) && $dates['stop']) {
                    $calculatedFields->setData('orders_expire', $dates['stop']->format('Y-m-d H:i:s'));
                }
                try {
                    $calculatedFields->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

    /**
     * List all queued messages and send its
     * 
     * @return void
     */
    public function sendQueuedMessages()
    {
        // get lock
        if ($this->_getLock(self::SEND_MESSAGE_PROCESS_ID)) {
            //load queued message list
            $queuedMessages = Mage::getModel('dolist/dolistemt_message_queued')
                                ->getCollection()
                                ->setOrder('id', 'ASC');
            //prepare collection by template
            $messagesByTemplates = array();
            foreach ($queuedMessages as $message) {
                $messagesByTemplates[$message->getTemplateId()][] = $message;
            }
            
            foreach ($messagesByTemplates as $templateId => $templateMessages) {
                foreach($templateMessages as $message) {
                    try {
                        //prepare message
                        $messageContent = unserialize($message->getSerializedMessage());
                        // try to send message
                        Mage::getSingleton('dolist/service')->callDolistEmtSendmail($messageContent, $templateId, $message->getStoreId());
                        //remove from queue
                        $message->delete();
                        
                    } catch (SoapFault $fault) {
                        $queue = Mage::helper('dolist/queue');
                        /// All temporary errors (including "limit reached") should be requeued
                        if ($queue->isTemporaryError($fault)) {
                            Mage::log($fault);
                            $newQueuedMessage = clone $message;
                            //remove from queue and re-queue it
                            $message->delete();
                            $newQueuedMessage->setId(null)->save();
                        //else log & remove
                        } else {
                            $message->delete();
                            Mage::helper('dolist/log')->logError($message, $fault, 'SendMessage');
                        }
                        //if the error is a "limit reached" break to the next template
                        if ($queue->isLimitReachedError($fault)) {
                            break;
                        } 
                    }
                }
            }
            
            //release the lock
            $this->_releaseLock(self::SEND_MESSAGE_PROCESS_ID);
        }
    }
    
    /**
     * put a lock to avoid duplicate processing
     * 
     * @param string $processId
     * @return bool true if the scrip has been locked, false if it was already locked by another process
     */
    protected function _getLock($processId)
    {
        if (isset($this->_processes[$processId]) && $this->_processes[$processId]->isLocked()) {
            return false;
        }
        $this->_processes[$processId] = Mage::getModel('index/process')->setId($processId);
        $this->_processes[$processId]->lock();
        return true;
    }
    /**
     * realease a lock
     * 
     * @param string $processId
     */
    protected function _releaseLock($processId)
    {
        $this->_processes[$processId]->unlock();
    }
    
    /**
     * Import contacts from Dolist-V8 to Magento
     * Use dolist_status field from newsletter_subscriber table
     * 
     * @return void
     */
    public function contactImport()
    {
        // Loop on every website
        $websites = Mage::app()->getWebsites();
        $needToLoop = false;
        foreach ($websites as $website) {
            // Get website default store id
            $storeId = $website->getDefaultStore()->getId();
        
            if ($this->_getHelper()->isDolistV8Enabled($storeId)) {
                // Enforce translations loading
                Mage::getSingleton('core/translate')->init('frontend');

                /** @var Dolist_Net_Model_Service $service */
                $service = Mage::getSingleton('dolist/service');
                $result = $service->dolistV8ContactImport($storeId);
                if(!$result) {
                    $needToLoop = true;
                }
            }
        }

        if($needToLoop) {
            // Schedule new execution
            /** @var Mage_Cron_Model_Schedule $schedule */
            $schedule = Mage::getModel('cron/schedule');

            $schedule->setJobCode('dolist_contact_import')
                ->setCreatedAt(now())
                ->setScheduledAt(now())
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
                ->save();
        }
    }
    
    /**
     * Perform full export
     * 
     * @return void
     */
    public function fullExport()
    {
        $this->_getHelper()->logError('Starting full customer export');
        $process = new Mage_Index_Model_Process();
        $process->setId("full_export");
        if($process->isLocked()){
            return;
        }
        $process->lockAndBlock();

        // Retrieve store list to full export
        $fullExportStoreIds = $this->_getHelper()->getExportStoreIds('full');
        Mage::unregister('pause_export');

        foreach ($fullExportStoreIds as $storeId) {
            $startPagination = $this->_getHelper()->getExportPaginationStart('full_export', $storeId);
            if($startPagination == 0){
                continue;
            }

            if ($this->_getHelper()->isDolistV8Enabled($storeId)) {
                /** @var Dolist_Net_Model_Service $service */
                $service = Mage::getSingleton('dolist/service');
                $service->dolistV8FullExport($storeId, $startPagination);

                if (Mage::registry('pause_export') !== null) {
                    $this->_getHelper()->reScheduleDolistTask('full_export');
                    break;
                }
            }
        }
        $process->unlock();
    }
    
    /**
     * Differential export contacts from Magento to Dolist-V8
     * 
     * @return void
     */
    public function differentialExport()
    {
        $this->_getHelper()->logDebug('Starting differential customer export');

        $process = new Mage_Index_Model_Process();
        $process->setId("differential_export");
        if($process->isLocked()){
            $this->_getHelper()->logDebug('Starting differential customer export');
            return;
        }
        $this->_getHelper()->logDebug('Export observer before lock');

        $process->lockAndBlock();
        // Retrieve store list to full export
        $differentialExportStoreIds = $this->_getHelper()->getExportStoreIds('differential');
        Mage::unregister('pause_export');

        foreach ($differentialExportStoreIds as $storeId) {
            $this->_getHelper()->logDebug('Starting differential customer export for store : "' . $storeId .'"');

            $startPagination = $this->_getHelper()->getExportPaginationStart('differential_export', $storeId);
            if($startPagination == 0){
                $this->_getHelper()->logDebug('Startpagination to 0. break processing for store.');
            	continue;
            }

            if ($this->_getHelper()->isDolistV8Enabled($storeId) && $this->_isAllowedDifferentialExport($storeId)) {
                /** @var Dolist_Net_Model_Service $service */
                $service = Mage::getSingleton('dolist/service');
                $service->dolistV8DifferentialExport($storeId, $startPagination);

                if (Mage::registry('pause_export') !== null) {
                    $this->_getHelper()->reScheduleDolistTask('differential_export');
                    break;
                }
            }
        }
        $process->unlock();
    }
    
    /**
     * Add all Dolist-V8 enabled stores to dolist_differential_export list (before dolist_differential_export)
     * Must be scheduled before dolist_differential_export
     * 
     * @return void
     */
    public function prepareNightlyDifferentialExport()
    {
        foreach (array_keys(Mage::app()->getStores()) as $storeId) {
            if ($this->_getHelper()->isDolistV8Enabled($storeId)) {
                $this->_getHelper()->addExportStoreId('differential', $storeId);
                $this->_getHelper()->addExportPaginationStart("differential_export", 1, $storeId);
                $this->_getHelper()->createReport('differential_export', $storeId);
            }
        }
    }
    
    /**
     * Return True if differential export can be performed,
     * ie if at least one full export has already been performed
     * 
     * @param int $storeId Store ID scope
     * 
     * @return boolean True if differential export can be performed
     */
    protected function _isAllowedDifferentialExport($storeId)
    {
        return !is_null($this->_getHelper()->getFlagDolistLastExport($storeId));
    }
    
    /**
     * Update updated_at field in newsletter_subscriber table
     * This field permits a differential export
     * 
     * @param Varien_Event_Observer $observer Observer
     * 
     * @return Dolist_Net_Model_Observer
     */
    public function updateSubscriber($observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        $origSubscriber = Mage::getModel('newsletter/subscriber')->load($subscriber->getId());

        if ($subscriber->getData('subscriber_status') != $origSubscriber->getData('subscriber_status')) {
            $subscriber->setUpdatedAt(time());
        }
        
        return $this;
    }
    
    /**
     * Warn customer after login if dolist_status gets an error
     * 
     * @param Varien_Event_Observer $observer Observer
     * 
     * @return Dolist_Net_Model_Observer
     */
    public function warnCustomerDolistStatus($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();
        if ($customerId) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($customerId, 'customer_id');
            
            if ($subscriber->getId()) {
                $errorMessage = $this->_getHelper()->getDolistStatusErrorMessage($subscriber->getDolistStatus(), 'front');
                
                if (!is_null($errorMessage)) {
                    
                    Mage::getSingleton('customer/session')->addNotice($this->_getHelper()->__($errorMessage));
                }
            }
        }
        
        return $this;
    }

    public function cronSegmentExport()
    {

        $this->_getHelper()->logDebug('Starting Segments export');
        $process = new Mage_Index_Model_Process();
        $process->setId("segment_export");
        if($process->isLocked()){
            $this->_getHelper()->logError('segment_export is locked');
            return;
        }
        $process->lockAndBlock();

        $segments = Mage::getModel('enterprise_customersegment/segment')->getCollection();

        /* @var $segment Dolist_Net_Model_Customersegment */
        foreach ($segments as $segment) {
            $exportSuccess = FALSE;
            if($this->_getHelper()->isSegmentExportCronEnabled($segment->getId())) {
                $this->_getHelper()->logDebug('segment ' . $segment->getName() .' export is set to be processed by cron');

                $exportSuccess = $this->_getHelper()->exportSegment($segment->getId());
                if($exportSuccess) {
                    $this->_getHelper()->logDebug('segment ' . $segment->getName() .' has been successfully exported by cron');
                } else {
                    $this->_getHelper()->logError('segment ' . $segment->getName() . ' has not been successfully exported by cron');
                }
            } else {
                $this->_getHelper()->logDebug('segment ' . $segment->getName() .' export is not set to be exported by cron ');
            }
        }

        $process->unlock();
        $this->_getHelper()->logDebug('Ending Segments export');
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
}
