<?php

/**
 * Dolist Helper
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_DOLIST_V8_ENABLED = 'dolist/dolist_v8/active';
    const XML_DOLIST_EMT_ENABLED = 'dolist/dolist_emt/active';
    const LOGFILE = 'dolist.log';
    const FLAG_DOLIST_LAST_EXPORT = 'dolist_last_export';
    const OTHER_COUNTRIES_CODE = 999;

    protected $_contactExportRowAdapter = null;
    protected $_optoutMapping = null;
    protected $_contactHeader = array(
        'Email',
        'SalutationId',
        'FirstName',
        'LastName',
        'Company',
        'Address 1',
        'Address 2',
        'Address 3',
        'ZipCode',
        'City',
        'CountryID',
        'Phone',
        'Fax',
        'Mobile Phone',
        'MsgFormatId',
        'SaleMgt',
        'Birthdate',
        'CustomDate1',
        'CustomDate2',
        'CustomDate3',
        'CustomStr1',
        'CustomStr2',
        'CustomStr3',
        'CustomStr4',
        'CustomStr5',
        'CustomStr6',
        'CustomStr7',
        'CustomStr8',
        'CustomStr9',
        'CustomStr10',
        'CustomStr11',
        'CustomStr12',
        'CustomStr13',
        'CustomStr14',
        'CustomStr15',
        'CustomStr16',
        'CustomStr17',
        'CustomStr18',
        'CustomStr19',
        'CustomStr20',
        'CustomStr21',
        'CustomStr22',
        'CustomStr23',
        'CustomStr24',
        'CustomStr25',
        'CustomStr26',
        'CustomStr27',
        'CustomStr28',
        'CustomStr29',
        'CustomStr30',
        'CustomInt1',
        'CustomInt2',
        'CustomInt3',
        'CustomInt4',
        'CustomInt5',
        'CustomInt6',
        'CustomInt7',
        'CustomInt8',
        'CustomInt9',
        'CustomInt10'
    );

    public function loadSubscriberByEmail($email, $storeId)
    {
        /** @var Mage_Newsletter_Model_Resource_Subscriber_Collection $subscriberCollection */
        $subscriberCollection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->addFilter('store_id', $storeId)
            ->addFilter('subscriber_email', $email);

        if ($subscriberCollection->count() >= 1) {
            return $subscriberCollection->getFirstItem();
        }

        return null;
    }

    public function loadSubscriberByCustomer(Mage_Customer_Model_Customer $customer, $storeId)
    {
        /** @var Mage_Newsletter_Model_Resource_Subscriber_Collection $subscriberCollection */
        $subscriberCollection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->addFilter('store_id', $storeId)
            ->addFilter('customer_id', $customer->getId());

        if ($subscriberCollection->count() >= 1) {
            return $subscriberCollection->getFirstItem();
        }

        $subscriberCollection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->addFilter('store_id', $storeId)
            ->addFilter('subscriber_email', $customer->getEmail());

        if ($subscriberCollection->count() >= 1) {
            return $subscriberCollection->getFirstItem();
        }

        return null;
    }

    /**
     * Check if Dolist-V8 is enabled
     *
     * @param type $store Scope
     *
     * @return boolean
     */
    public function isDolistV8Enabled($store = null)
    {
        return (
            Mage::getStoreConfig(self::XML_DOLIST_V8_ENABLED, $store) &&
            Mage::getStoreConfig(Dolist_Net_Model_Service::XML_DOLIST_V8_ACCOUNTID, $store) != '' &&
            Mage::getStoreConfig(Dolist_Net_Model_Service::XML_DOLIST_V8_AUTH_KEY, $store) != '' &&
            Mage::getStoreConfig(Dolist_Net_Model_Service::XML_DOLIST_V8_LOGIN, $store) != '' &&
            Mage::getStoreConfig(Dolist_Net_Model_Service::XML_DOLIST_V8_PASSWORD, $store) != ''
        );
    }

    /**
     * Check if sales_flat_order is present
     *
     * @return boolean
     */
    public function isFlatTableEnabled()
    {
        $currentVersion = Mage::getVersion();
        return (version_compare($currentVersion, '1.4.1') >= 0);
    }

    /**
     * Check if Dolist-EMT is enabled
     *
     * @param type $store Scope
     *
     * @return boolean
     */
    public function isDolistEmtEnabled($store = null)
    {
        return Mage::getStoreConfig(self::XML_DOLIST_EMT_ENABLED, $store);
    }

    /**
     * Check if customer segments are enabled for this instance
     *
     * @return boolean
     */
    public function isCustomerSegmentEnabled()
    {
        $isEnabled = false;
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        if (isset($modulesArray['Enterprise_CustomerSegment'])) {
            $isEnabled = true;
        }
        return $isEnabled;
    }

    /**
     * Return dolist_last_export flag (in core_flag table) if it exists, null otherwise
     *
     * @param int $storeId Store id Default 0 => default config
     *
     * @return Mage_Core_Model_Flag|null Flag
     */
    public function getFlagDolistLastExport($storeId = 0)
    {
        // Use default store view
        if ($storeId == 0) {
            $storeId = Mage::app()->getDefaultStoreView();
        }

        // Look for last export date
        $flagCode = self::FLAG_DOLIST_LAST_EXPORT . '_' . $storeId;
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))
            ->load($flagCode, 'flag_code');

        return $flag;
    }

    /**
     * Retrieve Dolist status from OptoutEmail (retrieved from Dolist-V8 webservice GetContact method)
     *
     * @param int $optoutEmail OptoutEmail value
     *
     * @return int|null
     */
    public function getDolistStatus($optoutEmail)
    {
        $dolistStatus = null;

        if (is_null($this->_optoutMapping)) {
            $optoutMapping = array();
            $optoutConfig = (array)Mage::getConfig()->getNode('dolistparams/optout_mapping');

            foreach ($optoutConfig as $configItem) {
                $configItem = (array)$configItem;
                foreach (explode(',', $configItem['dolist_value']) as $value) {
                    $optoutMapping[$value] = $configItem['magento_value'];
                }
            }
            $this->_optoutMapping = $optoutMapping;
        }
        if (array_key_exists($optoutEmail, $this->_optoutMapping)) {
            $dolistStatus = $this->_optoutMapping[$optoutEmail];
        }

        return $dolistStatus;
    }

    /**
     * Return warning message to display when customer logins or when admin edits customer in back-office
     * Depends on dolist_status value in newsletter_subscriber table (got from OptoutEmail values)
     * 5 - Temporary error
     * 6 - Final error
     * 7 - Spam
     *
     * @param string $dolistStatus Dolist status
     * @param string $scope Scope 'front' or 'back'
     *
     * @return string Error message
     */
    public function getDolistStatusErrorMessage($dolistStatus, $scope = 'front')
    {
        $errorMessage = null;

        if ($dolistStatus != '') {
            switch ($scope) {
                case 'front':
                    switch ($dolistStatus) {
                        case '5':
                            $errorMessage = 'Your email address is in temporary error.';
                            break;
                        case '6':
                            $errorMessage = 'Your email address is in final error. Please correct it.';
                            break;
                        case '7':
                            $errorMessage = 'Last sent message was signaled as spam.';
                            break;
                    }
                    break;

                case 'back':
                    switch ($dolistStatus) {
                        case '5':
                            $errorMessage = 'This contact email address is in temporary error.';
                            break;
                        case '6':
                            $errorMessage = 'This contact email address is in final error. Please correct it.';
                            break;
                        case '7':
                            $errorMessage = 'Last message sent from this contact was signaled as spam.';
                            break;
                    }
                    break;
            }
        }

        return $errorMessage;
    }

    /**
     * Retrieve from core_flag table Dolist Exported Segment List
     * If null, instantiate new object
     *
     * @param int $storeId Store ID scope
     *
     * @return Mage_Core_Model_Flag
     */
    public function getDolistExportedSegmentList($storeId = 0)
    {
        $flagCode = 'dolist_exported_segment_list_' . $storeId;

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');

        if ($flag->getId() == null) {
            // Instanciate new object
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->save();
        }

        return $flag;
    }

    /**
     * Retur true if this segment is already exported
     *
     * @param int $segmentId Segment ID
     * @param int|array $stores Can be store ID or array of store ids
     *
     * @return boolean
     */
    public function isExportedSegment($segmentId, $stores = 0)
    {
        $return = false;

        if (is_array($stores)) {
            $return = false;
            foreach ($stores as $storeId) {
                $resultItem = $this->isExportedSegment($segmentId, $storeId);
                if ($resultItem == true) {
                    $return = true;
                    break;
                }
            }
        } else {
            $flag = $this->getDolistExportedSegmentList($stores);
            $dolistExportedSegmentList = $flag->getFlagData();

            if (
                is_array($dolistExportedSegmentList) &&
                array_search($segmentId, $dolistExportedSegmentList) !== false
            ) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Add exported segment to list stored in core_flag table
     *
     * @param int $segmentId Segment ID
     * @param int $storeId Store ID scope
     *
     * @return void
     */
    public function addExportedSegment($segmentId, $storeId = 0)
    {
        $flag = $this->getDolistExportedSegmentList($storeId);
        $dolistExportedSegmentList = $flag->getFlagData();

        if (!is_array($dolistExportedSegmentList)) {
            $dolistExportedSegmentList = array($segmentId);
        } else if (array_search($segmentId, $dolistExportedSegmentList) === false) {
            // Add segmentId if not found
            $dolistExportedSegmentList[] = $segmentId;
        }

        // To avoid duplicate data
        if (is_array($dolistExportedSegmentList)) {
            $dolistExportedSegmentList = array_unique($dolistExportedSegmentList);
        }

        $flag->setFlagData($dolistExportedSegmentList)
            ->save();
    }

    /**
     * Remove exported segment to list stored in core_flag table
     *
     * @param int $segmentId Segment ID
     * @param int $storeId Store ID scope
     *
     * @return void
     */
    public function removeExportedSegment($segmentId, $storeId = 0)
    {
        $flag = $this->getDolistExportedSegmentList($storeId);
        $dolistExportedSegmentList = $flag->getFlagData();

        if (
            is_array($dolistExportedSegmentList) &&
            array_search($segmentId, $dolistExportedSegmentList) !== false
        ) {
            unset($dolistExportedSegmentList[array_search($segmentId, $dolistExportedSegmentList)]);
        }

        // To avoid duplicate data
        if (is_array($dolistExportedSegmentList)) {
            $dolistExportedSegmentList = array_unique($dolistExportedSegmentList);
        }

        $flag->setFlagData($dolistExportedSegmentList)
            ->save();
    }

    /**
     * Retrieve from core_flag table Dolist  Segment Export Cronned Status flag
     * If null, instantiate new object
     *
     * @param int $segmentId Segment ID
     *
     * @return Mage_Core_Model_Flag
     */
    public function getCronExportSegmentFlag($segmentId)
    {
        $flagCode = 'dolist_cron_export_segment_' . $segmentId;

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');
        if ($flag->getId() == null) {
            // Instanciate new object
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->setFlagData(FALSE);
            $flag->save();
        }
        return $flag;
    }

    /**
     * Add Segment Export to Cronned segment export list
     *
     * @param $segmentId
     *
     * @return void
     */
    public function addSegmentExportToCron($segmentId)
    {

        $flag = $this->getCronExportSegmentFlag($segmentId);
        $flag->setFlagData(TRUE)
            ->save();
    }

    /**
     * Remove Segment Export to Cronned segment export list
     *
     * @param $segmentId
     *
     * @return void
     */
    public function removeSegmentExportFromCron($segmentId)
    {
        $flag = $this->getCronExportSegmentFlag($segmentId);
        $flag->setFlagData(FALSE)
            ->save();
    }

    /**
     * Return TRUE is the segment export is added to the cron, FALSE otherwise
     *
     * @param $segmentId
     *
     * @return bool
     */
    public function isSegmentExportCronEnabled($segmentId)
    {
        $flag = $this->getCronExportSegmentFlag($segmentId);
        return (bool) $flag->getFlagData();
    }

    public function exportSegment($segmentId) {
        $return = FALSE;

        $segment = Mage::getModel('enterprise_customersegment/segment')->load($segmentId);

        if($segment && $segment->getId()) {

            /**
             * Preparation des paramètres
             */
            $segmentWebsiteIds = $segment->getWebsiteIds();
            // Replace segment website ids with segment website default store view ids
            $segmentStoreIds = array();
            foreach ($segmentWebsiteIds as $segmentWebsiteId) {
                $website = Mage::app()->getWebsite($segmentWebsiteId);
                $segmentStoreIds[$segmentWebsiteId] = $website->getDefaultStore()->getId();
            }

            $enabledStoreIds = array();
            foreach ($segmentStoreIds as $segmentWebsiteId => $segmentStoreId) {
                if ($this->isDolistV8Enabled($segmentStoreId)) {
                    $enabledStoreIds[$segmentWebsiteId] = $segmentStoreId;
                }
            }

            $websiteIds = array_keys($enabledStoreIds); // Dolist-V8 enabled websites
            $storeIds   = $enabledStoreIds;
            $filteredWebsiteIds = $this->filterDistinctEnabledWebsites($websiteIds);

            /** Export */
            /** @var Dolist_Net_Model_Service $service */
            $service = Mage::getSingleton('dolist/service');
            $return = $service->exportSegment($segment->getId(), $filteredWebsiteIds, $storeIds);
        }
        return $return;

    }

    /**
     * Return Dolist-V8 export contact file header fields
     *
     * @return array Header fields
     */
    public function getDolistExportContactFileHeader()
    {
        return $this->_contactHeader;
    }

    /**
     * Return config data for all attribute codes in config file
     * Load correct row adapter, set default if nothing else specified in config
     *
     * @return array of Varien_Object
     */
    public function getContactExportRowAdapterConfig()
    {

        // Avoid to reload config if already loaded
        if (is_null($this->_contactExportRowAdapter)) {

            // Load data from config.xml
            $configAttributes = Mage::getConfig()->getNode('dolistparams/contact_export_row_adapter')->asArray();

            foreach ($configAttributes as $k => $val) {

                // Use a Varien_Object, more handleable than array
                $config = new Varien_Object($val);

                if (is_null($config->getAdapter())) {
                    // Default adapter
                    $config->setAdapter('dolist/dolistv8_export_adapter_default');
                }

                // Set adapter
                $adapter = Mage::getResourceModel($config->getAdapter());

                // Check if adapter exists
                if ($adapter === false) {
                    Mage::throwException('Adapter ' . $config->getAdapter() . ' does not exist.');
                } else {
                    $config->setAdapter($adapter);
                }

                $configAttributes[$k] = $config;
            }

            $this->_contactExportRowAdapter = $configAttributes;
        }

        return $this->_contactExportRowAdapter;
    }

    /**
     * Return config data for given $attributeCode
     *
     * @param string $attributeCode Attribute code
     *
     * @return Varien_Object
     */
    public function getContactExportRowAdapter($attributeCode)
    {
        $config = $this->_getContactExportRowAdapterConfig();

        // Throw exception if attribute code is not in config file
        if (!(array_key_exists($attributeCode, $config))) {
            Mage::throwException('Attribute code ' . $attributeCode . ' does not exist in config file');
        }

        return $config[$attributeCode];
    }

    /**
     * Retrieve default contact export row adapter
     *
     * @return mixed
     */
    public function getDefaultContactExportRowAdapter()
    {
        return Mage::getResourceModel('dolist/dolistv8_export_adapter_default');
    }

    /**
     * Return Dolist-V8 enabled website ids with distinct Dolist-V8 AccountID
     * Avoid several sends to same account
     * In return array structure, key is config website id, and value is an array containing
     * website ids with same Dolist-V8 config, ie same AccountID
     *
     * @param array $websiteIds Dolist-V8 enabled website ids
     *
     * @return array
     */
    public function filterDistinctEnabledWebsites($websiteIds)
    {
        $filteredWebsiteIds = array();

        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }
        asort($websiteIds);

        foreach ($websiteIds as $websiteId) {

            $accountId = Mage::getStoreConfig(
                Dolist_Net_Model_Service::XML_DOLIST_V8_ACCOUNTID,
                Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId()
            );
            $sameIdWebsiteList = array();

            // Only if Dolist-V8 is enabled on this scope
            if ($this->isDolistV8Enabled(Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId())) {

                // Loop again on website ids to group items with same AccountID
                foreach ($websiteIds as $websiteId2) {
                    $accountId2 = Mage::getStoreConfig(
                        Dolist_Net_Model_Service::XML_DOLIST_V8_ACCOUNTID,
                        Mage::app()->getWebsite($websiteId2)->getDefaultStore()->getId()
                    );

                    if (
                        $this->isDolistV8Enabled(Mage::app()->getWebsite($websiteId2)->getDefaultStore()->getId()) &&
                        $accountId == $accountId2
                    ) {
                        $sameIdWebsiteList[] = $websiteId2;
                    }

                }
                $filteredWebsiteIds[$websiteId] = $sameIdWebsiteList;
            }
        }

        $filteredList = $this->objectUnique($filteredWebsiteIds);
        return $filteredList;
    }

    /**
     * Return unique values for given array
     * array_unique cannot compare arrays, this function can
     *
     * @param array $array Array to filter
     *
     * @return array Filtered array
     */
    public function objectUnique($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->objectUnique($value);
            }
        }
        return $result;
    }

    /**
     * Retrieve pagination to perform Dolist-V8 full or differential export on
     *
     * @param string $scope Export scope. Can be full or differential
     *
     * @return int starting row to perform Dolist-V8 full or differential export on
     */
    public function getExportPaginationStart($scope, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_store_' . $storeId . '_pagination_start';
        $storeList = array();

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');

        if ($flag->getId() != null) {
            $start = $flag->getFlagData();
        } else {
            $this->addExportPaginationStart($scope, 1, $storeId);
            $start = 1;
        }

        return $start;
    }

    /**
     * Change pagination starting point to export
     *
     * @param string $scope Export scope. Can be full or differential
     * @param int $start Page id
     *
     */
    public function addExportPaginationStart($scope, $start, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_store_' . $storeId . '_pagination_start';

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');
        if ($flag->getId() == null) {
            // Instanciate new object
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->save();
        }

        $flag->setFlagData($start)
            ->save();
    }

    /**
     * @param $total
     * @param $scope
     * @param $storeId
     * @return mixed
     */
    public function setExportTotalCount($total, $scope, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_store_' . $storeId . '_total_count';

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');
        if ($flag->getId() == null) {
            // Instanciate new object
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->save();
        }

        $flag->setFlagData($total)
            ->save();
        return $total;
    }

    /**
     * @param $scope
     * @param $storeId
     * @return mixed
     */
    public function getExportTotalCount($scope, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_store_' . $storeId . '_total_count';

        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');

        if ($flag->getId() != null) {
            return $flag->getFlagData();
        }
    }

    public function reScheduleDolistTask($scope)
    {
        $jobCode = 'dolist_' . $scope;

        // Check if this job is not already scheduled
        $scheduleCollection = Mage::getSingleton('cron/schedule')->getCollection()
            ->addFieldToFilter('job_code', array('eq' => $jobCode))
            ->addFieldToFilter('executed_at', array('null' => true))
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
            $schedule = Mage::getModel('cron/schedule');

            $schedule->setJobCode($jobCode)
                ->setCreatedAt(now())
                ->setScheduledAt(now())
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
                ->save();
        }
    }

    /**
     * Retrieve store list to perform Dolist-V8 full or differential export on
     *
     * @param string $scope Export scope. Can be full or differential
     *
     * @return array Store to perform Dolist-V8 full or differential export on
     */
    public function getExportStoreIds($scope)
    {
        $flagCode = 'dolist_' . $scope . '_export_store_list';
        $storeList = array();

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');

        if ($flag->getId() != null) {
            $storeList = $flag->getFlagData();
        }

        return $storeList;
    }

    /**
     * Add store ID to export store list
     *
     * @param string $scope Export scope. Can be full or differential
     * @param int $storeId Store ID
     *
     * @return array Store to perform Dolist-V8 full or differential export on
     */
    public function addExportStoreId($scope, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_export_store_list';

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');
        if ($flag->getId() == null) {
            // Instanciate new object
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->save();
        }

        $storeList = $flag->getFlagData();

        if (!is_array($storeList)) {
            $storeList = array($storeId);
        } else if (array_search($storeId, $storeList) === false) {
            // Add segmentId if not found
            $storeList[] = $storeId;
        }

        // To avoid duplicate data
        if (is_array($storeList)) {
            $storeList = array_unique($storeList);
        }

        $flag->setFlagData($storeList)
            ->save();
    }

    /**
     * Remove store ID from export store list
     *
     * @param string $scope Export scope. Can be full or differential
     * @param int $storeId Store ID
     *
     * @return array Store to perform Dolist-V8 full or differential export on
     */
    public function removeExportStoreId($scope, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_export_store_list';

        // First, try to retrieve object
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');

        // Nothing to do if flag does not exist
        if ($flag->getId() != null) {
            $storeList = $flag->getFlagData();

            if (
                is_array($storeList) &&
                array_search($storeId, $storeList) !== false
            ) {
                unset($storeList[array_search($storeId, $storeList)]);
            }

            // To avoid duplicate data
            if (is_array($storeList)) {
                $storeList = array_unique($storeList);
            }

            $flag->setFlagData($storeList)
                ->save();
        }
    }

    /**
     * Log errors in custom logfile (dolist.log)
     *
     * @param string $message Log message
     *
     * @return void
     */
    public function logDebug($message)
    {
        Mage::log($message, Zend_Log::DEBUG, self::LOGFILE);
    }

    /**
     * Log errors in custom logfile (dolist.log)
     *
     * @param string $message Log message
     *
     * @return void
     */
    public function logError($message)
    {
        Mage::log($message, Zend_Log::ERR, self::LOGFILE);
    }


    /**
     * @param $scope
     * @param $storeId
     * @return int
     */
    public function createReport($scope, $storeId)
    {
        $flagCode = 'dolist_' . $scope . '_store_' . $storeId . '_report';

        // First, try to retrieve object
        /** @var Mage_Core_Model_Flag $flag */
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))->load($flagCode, 'flag_code');
        if ($flag->getId() == null) {
            // Instanciate new object
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->save();
        }

        $store = Mage::app()->getStore($storeId);

        /** @var Dolist_Net_Model_Reports $report */
        $report = Mage::getModel('dolist/reports');
        $report->setData(array(
            'type' => 'export',
            'name' => Mage::helper('dolist')->__(($scope == 'full_export') ? 'Full Export' : 'Differential Export') . ' (' . $store->getWebsite()->getName() . '-' . $store->getName() . ')'
        ));
        $report->save();

        Mage::log('create flag report : ' . $report->getId());
        // fix report id for the next export dolist
        $flag->setFlagData((int)$report->getId())->save();
        return (int)$report->getId();
    }

    public function getCurrentReportId($scope, $storeId)
    {
        if ($storeId == 0) {
            $storeId = Mage::app()->getDefaultStoreView();
        }

        // Look for last export report
        $flagCode = 'dolist_' . $scope . '_store_' . $storeId . '_report';
        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode))
            ->load($flagCode, 'flag_code');

        return $flag->getFlagData();
    }

    public function getTablename($table)
    {
        return (string)Mage::getConfig()->getTablePrefix() . $table;
    }
}
