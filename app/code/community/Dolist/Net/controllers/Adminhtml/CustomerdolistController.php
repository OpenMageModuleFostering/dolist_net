<?php

/**
 * Admin Dolist customer dolist controller (new screen in Back Office)
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Adminhtml_CustomerdolistController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/customerdolist');
    }

    /**
     * index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('customer/customerdolist');
        $this->_addBreadcrumb(
            $this->_getHelper()->__('Dolist'), $this->_getHelper()->__('ERP Dolist'));

        $this->renderLayout();
    }

    /**
     *
     */
    public function reportAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('customer/customerdolist');
        $this->_addBreadcrumb(
            $this->_getHelper()->__('Dolist'), $this->_getHelper()->__('ERP Dolist'));
        $this->_addContent($this->getLayout()->createBlock('dolist/adminhtml_report'));

        $this->renderLayout();
    }

    /**
     *
     */
    public function saveAction()
    {

        if ($data = $this->getRequest()->getPost()) {
            $storeId = $data['store_id'];

            try {
                $isMappingChanged = false;

                if ((string)$data['calculatedfieds_mode'] == '2') {
                    $dt = DateTime::createFromFormat("Y-m-d", $data['calculatedfieds_date']);
                    if ($dt === false || array_sum($dt->getLastErrors()) > 0) {
                        throw new \Exception($this->_getHelper()->__('When you choose “From a specified start date“ for the time period of calculated fields, you must provide a start date.'));
                    }
                }

                $config = Mage::getConfig();
                $config->saveConfig('dolist/dolist_v8/export_customer_with_order', $data['export_customer_with_order'], $storeId == 0 ? 'default' : 'store', $storeId);

                $oldCalculatedfiedsMode = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_mode', $storeId);
                $oldCalculatedfiedsDate = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_date', $storeId);

                if($oldCalculatedfiedsMode != $data['calculatedfieds_mode'] || $oldCalculatedfiedsDate != $data['calculatedfieds_date']) {
                    $config->saveConfig('dolist/dolist_v8/calculatedfieds_mode', $data['calculatedfieds_mode'], $storeId == 0 ? 'default' : 'store', $storeId);
                    $config->saveConfig('dolist/dolist_v8/calculatedfieds_date', $data['calculatedfieds_date'], $storeId == 0 ? 'default' : 'store', $storeId);
                    $isMappingChanged = true;
                }

                /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
                $collection = Mage::getModel('dolist/dolistv8_customfields')->getCollection();
                $collection->addFieldToFilter('scope_id', $storeId);

                $transform = array();

                foreach ($data['cstfieldStr'] as $item) {
                    $transform['cstfield_' . $item['magento_customer_attribute']] = $item['dolist_custom_fields'];
                }

                foreach ($data['cstfieldInt'] as $item) {
                    $transform['cstfield_' . $item['magento_customer_attribute']] = $item['dolist_custom_fields'];
                }

                foreach ($data['cstfieldDate'] as $item) {
                    $transform['cstfield_' . $item['magento_customer_attribute']] = $item['dolist_custom_fields'];
                }

                foreach ($collection as $customField) {
                    /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */
                    $dolistName = $customField->getData('name');

                    if (array_key_exists($dolistName, Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {
                        $magentoName = Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName[$dolistName];
                        if (array_key_exists(sprintf('cstfield_%s', $magentoName), $data) && $data[sprintf('cstfield_%s', $magentoName)] == '1') {
                            if ($customField->getData('magento_field') != $magentoName) {
                                $isMappingChanged = true;
                                $customField->setData('magento_field', $magentoName);
                            }
                        } else {
                            if ($customField->getData('magento_field') != null) {
                                $isMappingChanged = true;
                                $customField->setData('magento_field', null);
                            }
                        }

                    } else {
                        if (!in_array($dolistName, $transform)) {
                            if ($customField->getData('magento_field') != null) {
                                $isMappingChanged = true;
                                $customField->setData('magento_field', null);
                            }
                        } else {
                            $key = array_search($dolistName, $transform);
                            $magentoField = substr($key, strlen('cstfield_'));
                            if ($customField->getData('magento_field') != $magentoField) {
                                $isMappingChanged = true;
                                $customField->setData('magento_field', $magentoField);
                            }
                        }
                    }
                    $customField->save();
                }

                if ($isMappingChanged) {
                    if ($storeId == 0) {
                        $stores = $this->_getStoreList($storeId);
                        foreach ($stores as $_storeId) {
                            // Full Export
                            $flagCode = 'dolist_differential_export_filename_' . $_storeId;
                            $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
                            $flag->delete();

                            // Differential Export
                            $flagCode = 'dolist_full_export_filename_' . $_storeId;
                            $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
                            $flag->delete();

                            // Remove flag for full export to block differential
                            $flagCode = Dolist_Net_Helper_Data::FLAG_DOLIST_LAST_EXPORT . '_' . $_storeId;
                            $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
                            $flag->delete();
                        }

                    } else {
                        // Full Export
                        $flagCode = 'dolist_differential_export_filename_' . $storeId;
                        $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
                        $flag->delete();

                        // Differential Export
                        $flagCode = 'dolist_full_export_filename_' . $storeId;
                        $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
                        $flag->delete();

                        // Remove flag for full export to block differential
                        $flagCode = Dolist_Net_Helper_Data::FLAG_DOLIST_LAST_EXPORT . '_' . $storeId;
                        $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
                        $flag->delete();
                    }
                }

                $this->_getSession()->addSuccess($this->_getHelper()->__('Saved.'));

                Mage::app()->getCacheInstance()->cleanType('config');
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/', array('store' => $storeId));
                return;
            }
        }

        $this->_redirect('*/*/', array('store' => $storeId));
    }

    /**
     *
     */
    public function updateCustomFieldsAction()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $initStoreId = $storeId;

        /** @var Dolist_Net_Model_Service $dolistService */
        $dolistService = Mage::getModel('dolist/service');

        $result = $dolistService->dolistV8GetFieldList($storeId);

        if(!is_object($result)) {
            $successMessage = $this->_getHelper()->__('Unable to contact Dolist API.');
            $this->_getSession()->addSuccess($successMessage);

            $this->_redirect('*/*/', array('store' => $initStoreId));
            return;
        }

        foreach ($result->getData('FieldList')->Field as $field) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $model */
            $model = Mage::getModel('dolist/dolistv8_customfields');
            $model = $model->loadByNameAndScope($field->Name, $storeId);

            if (!$field->Access->CanDisplay) {
                $model->delete();
                continue;
            }

            $model->addData(array(
                'type' => $field->Type,
                'name' => $field->Name,
                'title' => $field->Title,
                'display' => $field->Display,
                'displayRank' => $field->DisplayRank,
                'translationKey' => $field->TranslationKey,
                'isCustom' => $field->IsCustom ? 1 : 0,
                'scope' => 'store',
                'scope_id' => $storeId
            ));

            $model->save();
        }

        $successMessage = $this->_getHelper()->__('Dolist custom field has been successfully updated.');
        $this->_getSession()->addSuccess($successMessage);

        $this->_redirect('*/*/', array('store' => $initStoreId));
    }

    /**
     * Schedule full export to Dolist-V8
     *
     * @return void
     * @throws Exception
     */
    public function fullExportAction()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $initStoreId = $storeId;
        $stores = $this->_getStoreList($storeId);

        // Loop on every store
        foreach ($stores as $storeId) {
            if ($this->_getHelper()->isDolistV8Enabled($storeId)) {
                $this->_getHelper()->createReport('full_export', $storeId);

                try {

                    $this->scheduleDolistTask('full', $storeId);

                    $this->_getHelper()->addExportPaginationStart("full_export", 1, $storeId);
                    $successMessage = $this->_getHelper()->__('Full export has been scheduled and will be performed next Cron crossing.')
                        . " (default store view id: " . $storeId . ")";

                    $this->_getSession()
                        ->addSuccess($successMessage);

                } catch (Exception $e) {
                    $errorMessage = $this->_getHelper()->__('Full export could not be scheduled.')
                        . " (default store view id: " . $storeId . ")";

                    $this->_getSession()
                        ->addError($errorMessage);
                }

            } else {
                $errorMessage = $this->_getHelper()->__("Dolist-V8 is not enabled for selected website.")
                    . " (default store view id: " . $storeId . ")";

                $this->_getSession()
                    ->addError($errorMessage);
            }
        }

        $this->_redirect('*/*/', array('store' => $initStoreId));

    }

    /**
     * Schedule differential export to Dolist-V8
     *
     * @return void
     * @throws Exception
     */
    public function differentialExportAction()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $initStoreId = $storeId;
        $stores = $this->_getStoreList($storeId);

        // Loop on every store
        foreach ($stores as $storeId) {

            if ($this->_getHelper()->isDolistV8Enabled($storeId)) {
                try {

                    // Look for last export date
                    // First, try to retrieve stored value
                    $flag = $this->_getHelper()->getFlagDolistLastExport($storeId);

                    // Throw exception if flag has never been set
                    if ($flag->getId() == null) {
                        throw new Exception(
                            $this->_getHelper()->__(
                                'At least one full export should have been perfomed before scheduling a differential export.'
                            ) . " (default store view id: " . $storeId . ")"
                        );
                    }

                    $this->_getHelper()->createReport('differential_export', $storeId);

                    $this->scheduleDolistTask('differential', $storeId);

                    $this->_getHelper()->addExportPaginationStart("differential_export", 1, $storeId);
                    $successMessage = $this->_getHelper()->__(
                            'Differential export has been scheduled and will be performed next time Cron runs.'
                        ) . " (default store view id: " . $storeId . ")";

                    $this->_getSession()->addSuccess($successMessage);

                } catch (Exception $e) {
                    $errorMessage = $e->getMessage();

                    $this->_getSession()->addError($errorMessage);

                    $this->_getHelper()->logError($errorMessage);
                }
            } else {
                $errorMessage = $this->_getHelper()->__("Dolist-V8 is not enabled for selected website.")
                    . " (default store view id: " . $storeId . ")";

                $this->_getSession()->addError($errorMessage);
            }
        }

        $this->_redirect('*/*/', array('store' => $initStoreId));
    }

    /**
     * Create new segment export for all already exported segments to Dolist-V8
     *
     * @return void
     */
    public function updateSegmentsAction()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $initStoreId = $storeId;
        $stores = $this->_getStoreList($storeId);
        $error = false;


        $process = new Mage_Index_Model_Process();
        $process->setId("segment_export");
        if($process->isLocked()){
            $this->_getHelper()->logError('segment_export is locked');
            $this->_getSession()->addError($this->_getHelper()->__('Another export is already running. Please try again later.'));
            // Redirect to segment edit page
            $this->_redirect('*/*/', array('store' => $initStoreId));
            return;
        }
        $process->lockAndBlock();

        // Loop on every store
        foreach ($stores as $storeId) {
            if ($this->_getHelper()->isDolistV8Enabled($storeId)) {

                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

                // Retrieve already exported segments
                $flag = $this->_getHelper()->getDolistExportedSegmentList($storeId);
                $dolistExportedSegmentList = $flag->getFlagData();

                if (!is_array($dolistExportedSegmentList) || empty($dolistExportedSegmentList)) {

                    // If nothing, cannot update
                    $this->_getSession()
                        ->addError(
                            $this->_getHelper()->__('Cannot update customer segments because no segment has been exported')
                            . " (default store view id: " . $storeId . ")"
                        );

                } else {

                    // Else perform new export for each one
                    /** @var Dolist_Net_Model_Service $service */
                    $service = Mage::getSingleton('dolist/service');
                    foreach ($dolistExportedSegmentList as $segmentId) {
                        $exportReturn = $service->exportSegment($segmentId, array($websiteId), array($storeId));

                        if (!$exportReturn) {
                            $error = true;
                        }
                    }
                }
            } else {
                $errorMessage = $this->_getHelper()->__("Dolist-V8 is not enabled for selected website.")
                    . " (default store view id: " . $storeId . ")";

                $this->_getSession()
                    ->addError($errorMessage);
            }
        }

        if ($error) {
            $this->_getSession()
                ->addError(
                    $this->_getHelper()
                        ->__('At least one error occured while segment updating. Please watch logs to get more details.')
                );
        }


        $process->unlock();


        $this->_redirect('*/*/', array('store' => $initStoreId));
    }

    /**
     * Schedule task for given $jobcode. Task will be performed at next cron running
     * Write in cron_schedule table
     *
     * @param string $scope Export scope. Can be full or differential
     * @param int $storeId Store id
     *
     * @return void
     * @throws Exception if export already running
     */
    public function scheduleDolistTask($scope, $storeId)
    {
        // Flag on current store id to be exported
        $this->_getHelper()->addExportStoreId($scope, $storeId);

        $jobCode = 'dolist_' . $scope . '_export';
        /** @var Mage_Cron_Model_Mysql4_Schedule_Collection $runningJobCollection */
        $runningJobCollection = Mage::getSingleton('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => $jobCode))
            ->addFieldToFilter(
                'status',
                array(
                    'in' => array(
                        Mage_Cron_Model_Schedule::STATUS_RUNNING
                    )
                )
            );

        if ($runningJobCollection->getSize() > 0) {
            $errorMessage = $this->_getHelper()->__("Another export is already running. Please try again later.");
            $this->_getSession()
                ->addError($errorMessage);
            throw new Exception($errorMessage);
        }

        // Check if this job is not already scheduled
        /** @var Mage_Cron_Model_Mysql4_Schedule_Collection $scheduleCollection */
        $scheduleCollection = Mage::getSingleton('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => $jobCode))
            ->addFieldToFilter(
                'status',
                array(
                    'in' => array(
                        Mage_Cron_Model_Schedule::STATUS_PENDING
                    )
                )
            );

        if ($scheduleCollection->getSize() == 0) {

            // Schedule a new task only if another one is not already pending
            /** @var Mage_Cron_Model_Schedule $schedule */
            $schedule = Mage::getModel('cron/schedule');
            $schedule->setJobCode($jobCode)
                ->setCreatedAt(now())
                ->setScheduledAt(now())
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
                ->save();

        }
    }

    /**
     * Perform specific customer segment export to Dolist-V8
     * Export is performed in live, this operation is not scheduled for later
     *
     * @return void
     */
    public function exportSegmentAction()
    {
        $segmentId = $this->getRequest()->getParam('segment_id');
        $process = new Mage_Index_Model_Process();
        $process->setId("segment_export");
        if($process->isLocked()){
            $this->_getHelper()->logError('segment_export is locked');
            $this->_getSession()->addError($this->_getHelper()->__('Another export is already running. Please try again later.'));
            // Redirect to segment edit page
            $this->_redirect('*/customersegment/edit/',array('id'=> $segmentId));
            return;
        }
        $process->lockAndBlock();

        // Force new segment on dolist platforme
        $flagCode = 'dolist_segment_filename_' . $segmentId;
        $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');
        $flag->delete();


        // Trigger Export
        $this->_getHelper()->exportSegment($segmentId);

        $process->unlock();

        // Redirect to segment index page
        $this->_redirect('*/customersegment/index');
    }

    /**
     * Remove segment from exported list
     *
     * @return void
     */
    public function removeSegmentAction()
    {
        // Load segment
        $segmentId = $this->getRequest()->getParam('segment_id');
        $storeIds = array_keys(Mage::app()->getStores());

        // Remove exported segments from all stores
        foreach ($storeIds as $storeId) {
            $this->_getHelper()->removeExportedSegment($segmentId, $storeId);
        }

        //we can assume that segment's Export should not be cronned
        $this->_getHelper()->removeSegmentExportFromCron($segmentId);


        // Redirect to segment index page
        $this->_redirect('*/customersegment/index');
    }

    /**
     * Add Segment Export to Magento Cron
     */
    public function addSegmentExportToCronAction() {
        // Load segment
        $segmentId = $this->getRequest()->getParam('segment_id');

        $this->_getHelper()->addSegmentExportToCron($segmentId);

        // Redirect to segment edit page
        $this->_redirect('*/customersegment/edit/',array('id'=> $segmentId));
    }

    /**
     * Remove Segment Export from Magento Cron
     */
    public function removeSegmentExportFromCronAction() {
        // Load segment
        $segmentId = $this->getRequest()->getParam('segment_id');

        $this->_getHelper()->removeSegmentExportFromCron($segmentId);

        // Redirect to segment edit page
        $this->_redirect('*/customersegment/edit/',array('id'=> $segmentId));
    }

    /**
     * Return array with default store view ids for each website
     *
     * @param int $storeId Store id
     *
     * @return array Store list
     */
    protected function _getStoreList($storeId = 0)
    {
        $stores = array();

        // If default config, retrieve all website default store ids
        if ($storeId == 0) {

            // Loop on every website
            $websites = Mage::app()->getWebsites();
            foreach ($websites as $website) {
                // Get website default store id
                /** @var Mage_Core_Model_Website $website */
                foreach($website->getStoreIds() as $_storeId) {
                    $stores[] = $_storeId;
                }
            }
        } else {
            // If store id not default, return it as array
            $stores = array($storeId);
        }

        return $stores;
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
     * Retrieve model helper
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
