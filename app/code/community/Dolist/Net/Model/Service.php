<?php

/**
 * Dolist SOAP adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service extends Varien_Object
{
    const CODE = 'dolist';
    const SOAP_VERSION = SOAP_1_1;

    const XML_DOLIST_V8_ACCOUNTID = 'dolist/dolist_v8/accountid';
    const XML_DOLIST_V8_AUTH_KEY = 'dolist/dolist_v8/authentication_key';
    const XML_DOLIST_V8_LOGIN = 'dolist/dolist_v8/login';
    const XML_DOLIST_V8_PASSWORD = 'dolist/dolist_v8/password';

    const XML_DOLIST_EMT_ACCOUNTID = 'dolist/dolist_emt/accountid';
    const XML_DOLIST_EMT_AUTH_KEY = 'dolist/dolist_emt/authentication_key';

    const MAGENTO_FULL_EXPORT_LABEL = 'MAGENTO - Chargement complet';
    const MAGENTO_DIFF_EXPORT_LABEL = 'MAGENTO - Chargement différentiel';

    private $_tables;

    /**
     * Send transactional email using Dolist-EMT webservice instead of native Magento method
     *
     * @param array $message Message parameters
     * @param $dolistTemplateId
     * @param array|int $storeId Store ID
     *
     * @return void
     */
    public function dolistEmtSendmail($message, $dolistTemplateId, $storeId = 0)
    {
        /** @var Dolist_Net_Helper_Queue $queue */
        $queue = Mage::helper('dolist/queue');

        if (Mage::getStoreConfig('dolist/dolist_emt/asynchronous') == 1) {
            $queue->queueMessage($dolistTemplateId, $message, $storeId);
        } else {
            try {
                $this->callDolistEmtSendmail($message, $dolistTemplateId, $storeId);
            } catch (SoapFault $fault) {
                $queue->queueMessage($dolistTemplateId, $message, $storeId);
            }
        }
    }

    /**
     * Send transactional email using Dolist-EMT webservice instead of native Magento method
     *
     * @param array $message Message parameters
     * @param $dolistTemplateId
     * @param array|int $storeId Store ID
     *
     * @throws Exception
     * @throws SoapFault
     * @return void
     */
    public function callDolistEmtSendmail($message, $dolistTemplateId, $storeId = 0)
    {
        try {
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/send_message/proxywsdl');

            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 0,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/send_message/location')
                )
            );

            // get auth key from SOAP
            $token = $this->_dolistEmtGetKey($storeId);

            $wsParmas = array(
                'store_id' => $storeId,
                'key' => $token['Key'],
                'message' => $message,
            );
            // Construct request
            $request = Mage::getModel('dolist/service_dolistemt_request_sendEmail', $wsParmas);

            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistemt_response_sendEmail');
            // Call web service
            $response = $response->addData((array)$client->SendMessage($request->toArray()));

        } catch (SoapFault $fault) {
            throw $fault;
        }
    }

    /**
     * Retrieve template list created with Dolist-EMT
     * This method is also used to test connection in Back-Office
     *
     * @param array|int $storeId Store ID
     *
     * @param string $accountId Optional account (used to test connection in back office)
     * @param null $authenticationKey
     * @internal param string $AuthKey Optional auth,ntification key (used to test connection in back office)
     * @return array Array of Dolist-EMT templates. Name indexed by Dolist-EMT ids
     */
    public function dolistEmtGetTemplateList($storeId = 0, $accountId = null, $authenticationKey = null)
    {
        $request = null;
        $response = null;

        try {
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/get_template_list/proxywsdl');
            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 1,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/get_template_list/location')
                )
            );

            // get auth key from SOAP
            $token = $this->_dolistEmtGetKey($storeId, $accountId, $authenticationKey);

            // Construct request
            $request = Mage::getModel('dolist/service_dolistemt_request_gettemplatelist', array('store_id' => $storeId, 'key' => $token['Key'], 'account_id' => $accountId));

            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistemt_response_gettemplatelist');
            // Call web service
            $response = $response->addData((array)$client->GetTemplateList($request->toArray()));

        } catch (SoapFault $fault) {
            $this->_logError($request, $fault, 'GetTemplateList');
        }

        return $response;
    }

    /**
     * Dolist-V8 authentication before every call to web services
     * Init web service connection
     *
     * @param int $storeId Store ID
     * @param string $accountId Optional account ID (used to test connection in back office)
     * @param string $authenticationKey Optional authentication Key (used to test connection in back office)
     *
     * @return Dolist_Net_Model_Service_Dolistv8_Response_Getauthenticationtoken Webservice response object
     */
    public function dolistV8GetAuthenticationToken($storeId = 0, $accountId = null, $authenticationKey = null)
    {
        $request = null;
        $response = null;

        try {
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/get_authentication_v8_token/proxywsdl');
            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 1,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/get_authentication_v8_token/location')
                )
            );

            // Construct request
            $request = Mage::getModel(
                'dolist/service_dolistv8_request_getauthenticationtoken',
                array(
                    'store_id' => $storeId,
                    'account_id' => $accountId,
                    'auth_key' => $authenticationKey
                )
            );

            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistv8_response_getauthenticationtoken');

            // Call web service
            $response = $response->addData((array)$client->GetAuthenticationToken($request->toArray()));

        } catch (SoapFault $fault) {
            $this->_logError($request, $fault, 'GetAuthenticationToken');
        }

        return $response;
    }


    /**
     * Dolist-Emt authentication before every call to web services
     * Init web service connection
     *
     * @param int $storeId Store ID
     * @param string $accountId Optional account ID (used to test connection in back office)
     * @param string $authenticationKey Optional authentication Key (used to test connection in back office)
     *
     * @return Dolist_Net_Model_Service_Dolistemt_Response_Getauthenticationtoken Webservice response object
     */
    public function dolistEmtGetAuthenticationToken($storeId = 0, $accountId = null, $authenticationKey = null)
    {
        $request = null;
        $response = null;

        try {
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/get_authentication_emt_token/proxywsdl');
            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 1,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/get_authentication_emt_token/location')
                )
            );

            // Construct request
            $request = Mage::getModel(
                'dolist/service_dolistemt_request_getauthenticationtoken',
                array(
                    'store_id' => $storeId,
                    'account_id' => $accountId,
                    'auth_key' => $authenticationKey
                )
            );
            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistemt_response_getauthenticationtoken');

            // Call web service
            $response = $response->addData((array)$client->GetAuthenticationToken($request->toArray()));

        } catch (SoapFault $fault) {
            $this->_logError($request, $fault, 'GetAuthenticationToken');
        }

        return $response;
    }

    /**
     * Call dolistV8GetAuthenticationToken method to retrieve authentication token
     * and to use it in other Dolist-V8 webservice methods
     *
     * @param int $storeId Store ID
     *
     * @return string Authentication token key
     */
    protected function _dolistV8GetKey($storeId = 0)
    {
        $key = "";

        $token = $this->dolistV8GetAuthenticationToken($storeId);
        if ($token) {
            $tokenData = $token->getData();
            if (is_array($tokenData) && array_key_exists('Key', $tokenData)) {
                $key = $tokenData['Key'];
            }
        }

        // Key could not be retrieved or is null
        if ($key == "") {
            Mage::helper('dolist')->logError('Authentication token key could not be retrieved or is null');
        }

        return $key;
    }

    /**
     * Call dolistEmtGetAuthenticationToken method to retrieve authentication token
     * and to use it in other Dolist-Emt webservice methods
     *
     * @param int $storeId Store ID
     *
     * @param null $accountId
     * @param null $authenticationKey
     * @return string Authentication token key
     */
    protected function _dolistEmtGetKey($storeId = 0, $accountId = null, $authenticationKey = null)
    {
        $key = "";
        $result = array('Key' => null);
        $token = $this->dolistEmtGetAuthenticationToken($storeId, $accountId, $authenticationKey);
        if ($token) {
            $tokenData = $token->getData();
            if (is_array($tokenData) && array_key_exists('Key', $tokenData)) {
                $result = array(
                    'Key' => $tokenData['Key'],
                    'DeprecatedDate' => $tokenData['DeprecatedDate'],
                );
            }
        }

        // Key could not be retrieved or is null
        if ($result['Key'] == "") {
            Mage::helper('dolist')->logError('Authentication token key could not be retrieved or is null');
        }

        return $result;
    }

    /**
     * Call CreateImport Dolist-V8 webservice method
     *
     * @param string $importName Import Name
     * @param bool $createSegment Does this import create segment or not. Default: false
     * @param int $storeId Store ID
     *
     * @return Dolist_Net_Model_Service_Dolistv8_Response_Createimport Webservice response object
     */
    public function dolistV8CreateImport($importName, $createSegment = false, $storeId = 0)
    {
        $request = null;
        $response = null;

        try {
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/create_import/proxywsdl');
            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 1,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/create_import/location')
                )
            );

            // Construct request
            $key = $this->_dolistV8GetKey($storeId);
            $request = Mage::getModel(
                'dolist/service_dolistv8_request_createimport',
                array(
                    'store_id' => $storeId,
                    'key' => $key,
                    'import_name' => $importName,
                    'create_segment' => $createSegment
                )
            );

            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistv8_response_createimport');

            // Call web service
            $response = $response->addData((array)$client->CreateImport($request->toArray()));

        } catch (SoapFault $fault) {
            $this->_logError($request, $fault, 'CreateImport');
            return false;
        }

        return $response;
    }

    /**
     * @param int $storeId
     * @return false|Mage_Core_Model_Abstract|null|Varien_Object
     */
    public function dolistV8GetFieldList($storeId = 0)
    {
        $request = null;
        $response = null;

        try {
            $key = $this->_dolistV8GetKey($storeId);
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/get_field_list/proxywsdl');

            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 1,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/get_field_list/location')
                )
            );

            // Construct request
            $request = Mage::getModel(
                'dolist/service_dolistv8_request_getfieldlist',
                array(
                    'store_id' => $storeId,
                    'key' => $key
                )
            );

            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistv8_response_getfieldlist');

            // Call web service
            $response = $response->addData((array)$client->GetFieldList($request->toArray()));

        } catch (SoapFault $fault) {
            $this->_logError($request, $fault, 'GetFieldList');
        }

        return $response;
    }

    /**
     * Call GetContact Dolist-V8 webservice method
     *
     * @param string $key Token authentication key
     * @param array|int $storeId Store ID
     *
     * @return Dolist_Net_Model_Service_Dolistv8_Response_Createimport Webservice response object
     */
    public function dolistV8GetContact($key, $storeId = 0)
    {
        $request = null;
        $response = null;

        try {
            $proxywsdl = (string)Mage::getConfig()->getNode('dolistparams/get_contact/proxywsdl');
            $client = new SoapClient(
                $proxywsdl,
                array(
                    'soap_version' => self::SOAP_VERSION,
                    'trace' => 1,
                    'location' => (string)Mage::getConfig()->getNode('dolistparams/get_contact/location')
                )
            );

            // Construct request
            $request = Mage::getModel(
                'dolist/service_dolistv8_request_getcontact',
                array(
                    'store_id' => $storeId,
                    'key' => $key
                )
            );

            // To use magic methods on response object
            $response = Mage::getModel('dolist/service_dolistv8_response_getcontact');

            // Call web service
            $response = $response->addData((array)$client->GetContact($request->toArray()));

        } catch (SoapFault $fault) {
            $this->_logError($request, $fault, 'GetContact');
        }

        return $response;
    }

    /**
     * Perfom contact import from Dolist-V8
     *
     * @param int $storeId Store ID
     *
     * @return boolean
     */
    public function dolistV8ContactImport($storeId = 0)
    {
        $key = $this->_dolistV8GetKey($storeId);
        $handled = 0;
        $maxHandled = Mage::getStoreConfig('dolist/dolist_v8/export_page_size');

        $response = $this->dolistV8GetContact($key, $storeId);
        $contactList = $response->getData('ContactList');
        $returnContactsCount = $response->getData('ReturnContactsCount');

        while ($returnContactsCount > 0 && $handled < $maxHandled) {
            $handled += $returnContactsCount;
            Mage::log('Handled : ' . $handled);

            $contactList = (array)$contactList;
            $contactList = $contactList['ContactData'];

            foreach ($contactList as $contact) {
                $contact = (array)$contact;

                $email = $contact['Email'];
                $optoutEmail = $contact['OptoutEmail'];

                // Look for contact in newsletter_subscriber table
                /** @var Mage_Newsletter_Model_Subscriber $subscriber */
                $subscriber = Mage::getModel('newsletter/subscriber')->load($email, 'subscriber_email');
                if ($subscriber->getId()) {
                    // If contact found, update its dolist_status value
                    $dolistStatus = $this->_getHelper()->getDolistStatus($optoutEmail);

                    // if dolistStatus found and different from store value
                    if (!is_null($dolistStatus) && $dolistStatus != $subscriber->getDolistStatus()) {
                        // Value to save in dolist_status field from newsletter_subscriber table
                        $subscriber->setDolistStatus($dolistStatus);

                        // Update subscriber status if maps to Magento status
                        if (
                            $dolistStatus >= Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
                            && $dolistStatus <= Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED
                        ) {
                            $subscriber->setSubscriberStatus($dolistStatus);
                        }

                        $subscriber->save();
                    }
                }
            }

            $response = $this->dolistV8GetContact($key, $storeId);
            $contactList = $response->getData('ContactList');
            $returnContactsCount = $response->getData('ReturnContactsCount');
        }

        return $returnContactsCount <= 0;
    }

    /**
     * Perform full export: all Magento contacts to Dolist-V8
     * This operation can be long, depending on your contact base size (customer and newsletter subscribers)
     *
     * @param int $storeId Store ID
     *
     * @param $startPagination
     * @throws Exception
     * @return void
     */
    public function dolistV8FullExport($storeId = 0, $startPagination)
    {
        if ($this->_getHelper()->isDolistV8Enabled($storeId)) {
            $report = null;

            try {
                $report = null;
                $reportId = $this->_getHelper()->getCurrentReportId('full_export', $storeId);

                if ($reportId) {
                    /** @var Dolist_Net_Model_Reports $report */
                    $report = Mage::getModel('dolist/reports')->load($reportId);
                }

                if ($report) {
                    $report->log(sprintf($this->_getHelper()->__('Starting full customer export Store id : %s'), $storeId));
                }

                if ($startPagination == 1) {
                    $query = $this->getCustomerIdCollection($startPagination, $storeId, true);

                    if (Mage::getStoreConfig('dev/log/active') && $report) {
                        $report->log($query->assemble());
                    }

                    $result = $query->query()->fetchAll();
                    $this->_getHelper()->setExportTotalCount($result[0]['total'], 'full_export', $storeId);

                    if ($result[0]['total'] == 0) {
                        // 4. Save dolist_last_export flag
                        // First, try to retrieve stored value
                        $flag = $this->_getHelper()->getFlagDolistLastExport($storeId);

                        // Instanciate new flag object only first time
                        if ($flag->getId() == null) {
                            $flag = Mage::getModel(
                                'core/flag',
                                array(
                                    'flag_code' => Dolist_Net_Helper_Data::FLAG_DOLIST_LAST_EXPORT . '_' . $storeId
                                )
                            );
                        }

                        $flag->setFlagData(time())->save();

                        // Remove flag on current store id to be exported
                        $this->_getHelper()->removeExportStoreId('full', $storeId);

                        $report->log($this->_getHelper()->__('Nothing to export'));
                        $report->start(0);
                        $report->end();
                        return;
                    }

                    if ($report) {
                        $report->start($result[0]['total']);
                    }
                }


                // 4 steps:
                // 1. Retrieve full export file name (either in configuration table, either from webservice)
                try {
                    $exportFileName = $this->retrieveFileName('full_export', $storeId);
                } catch (\Exception $ex) {
                    if ($report) {
                        $report->log($ex->__toString());
                        $report->end('error');
                    }

                    // Remove flag on current store id to be exported
                    $this->_getHelper()->removeExportStoreId('full', $storeId);

                    return;
                }

                // 2. Generate full export file
                $localFilename = $this->generateExportFile('full_export', $exportFileName, $storeId, $startPagination);

                if (!is_null($localFilename)) {

                    // 3. Push full export file on Dolist FTP server
                    $this->_getHelper()->addExportPaginationStart('full_export', 0, $storeId);
                    $this->pushExportFile($exportFileName, $localFilename, $storeId, $report);

                    // 4. Save dolist_last_export flag
                    // First, try to retrieve stored value
                    $flag = $this->_getHelper()->getFlagDolistLastExport($storeId);

                    // Instanciate new flag object only first time
                    if ($flag->getId() == null) {
                        $flag = Mage::getModel(
                            'core/flag',
                            array(
                                'flag_code' => Dolist_Net_Helper_Data::FLAG_DOLIST_LAST_EXPORT . '_' . $storeId
                            )
                        );
                    }

                    $flag->setFlagData(time())->save();

                    // Remove flag on current store id to be exported
                    $this->_getHelper()->removeExportStoreId('full', $storeId);

                    if ($report) {
                        $report->end();
                    }
                }
            } catch (Exception $ex) {
                if ($report) {
                    $report->log($ex->getMessage());
                    $report->end('failed');
                }

                throw $ex;
            }
        }
    }

    /**
     * Perform differential export: Magento contacts updated from last export (last export could be full or differential) to Dolist-V8
     * First export must be full export, to save dolistv8_last_export_date parameter
     *
     * @param int $storeId Store ID
     *
     * @param $startPagination
     * @return void
     */
    public function dolistV8DifferentialExport($storeId = 0, $startPagination)
    {
        if ($this->_getHelper()->isDolistV8Enabled($storeId)) {

            $report = null;
            $reportId = $this->_getHelper()->getCurrentReportId('differential_export', $storeId);

            if (!$reportId) {
                $reportId = $this->_getHelper()->createReport('differential_export', $storeId);
            }

            /** @var Dolist_Net_Model_Reports $report */
            $report = Mage::getModel('dolist/reports')->load($reportId);

            if ($report) {
                $report->log(sprintf($this->_getHelper()->__('Starting differential customer export Store id : %s'), $storeId));
            }

            if ($startPagination == 1) {
                $flag = $this->_getHelper()->getFlagDolistLastExport($storeId);

                // Log error if flag has never been set
                if ($flag->getId() == null) {
                    $this->_getHelper()->logError($this->_getHelper()->__('At least one full export should have been perfomed before generating differential export file.'));
                    if ($report) {
                        $report->log($this->_getHelper()->__('At least one full export should have been perfomed before generating differential export file.'));
                        $report->end('error');
                    }
                    $this->_getHelper()->removeExportStoreId('differential', $storeId);

                    // Stop processing if export never performed
                    return;

                }

                $lastExportTimestamp = $flag->getFlagData(); // timestamp format
                $query = $this->getCustomerIdCollection($startPagination, $storeId, true, $lastExportTimestamp);

                if (Mage::getStoreConfig('dev/log/active') && $report) {
                    $report->log($query->assemble());
                }

                $result = $query->query()->fetchAll();

                if ($result[0]['total'] == 0) {
                    if ($report) {
                        $report->log($this->_getHelper()->__('Nothing to export'));
                        $report->end();
                    }
                    $this->_getHelper()->removeExportStoreId('differential', $storeId);
                }

                $this->_getHelper()->setExportTotalCount($result[0]['total'], 'differential_export', $storeId);

                if ($report) {
                    $report->start($result[0]['total']);
                }
            }

            // 6 steps:
            // Store date at the beginning of process, because it can be long
            $now = time();
            // Export for differential subscribers
            $exportDiffPerformed = false;

            // 1. Retrieve differential export file name (either in configuration table, either from webservice)
            $exportFileName = $this->retrieveFileName('differential_export', $storeId);

            // 2. Generate differential export file
            $localFilename = $this->generateExportFile('differential_export', $exportFileName, $storeId, $startPagination);

            if (!is_null($localFilename)) {

                // 3. Push differential export file on Dolist FTP server
                $this->_getHelper()->addExportPaginationStart('differential', 0, $storeId);
                $exportDiffPerformed = $this->pushExportFile($exportFileName, $localFilename, $storeId, $report);
            }

            // 4. Save dolist_last_export flag; only if export has been performed
            if ($exportDiffPerformed) {

                // First, try to retrieve stored object
                $flag = $this->_getHelper()->getFlagDolistLastExport($storeId);

                if ($flag->getId() != null) {
                    $flag->setFlagData($now)
                        ->save();
                } else {
                    $this->_getHelper()->logError(
                        Mage::helper('dolist')->__(
                            'dolist_last_export flag should be set.'
                        )
                    );
                }

                // Remove flag on current store id to be exported
                $this->_getHelper()->removeExportStoreId('differential', $storeId);
                if ($report) {
                    $report->end();
                }
            }

        }
    }

    public function getCustomerIdCollection($startPagination, $storeId, $count = false, $differential = false)
    {
        $exportPageSize = Mage::getStoreConfig('dolist/dolist_v8/export_page_size', $storeId);
        $exportWithCustomer = Mage::getStoreConfig('dolist/dolist_v8/export_customer_with_order', $storeId);

        $query = null;
        if ($exportWithCustomer) {
            /** @var Varien_Db_Select $select1 */
            $select1 = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToSelect('customer_id')
                ->addFieldToFilter('main_table.store_id', $storeId)
                ->getSelect();
            $select1->join($this->getTable('customer_entity'), 'main_table.customer_id = ' . $this->getTable('customer_entity') . '.entity_id', null);

            $select2 = Mage::getModel('newsletter/subscriber')
                ->getCollection()
                //->addFieldToFilter('subscriber_status', Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
                ->addFieldToFilter('store_id', $storeId)
                ->getSelect();
            $select2->reset(Zend_Db_Select::COLUMNS);
            $select2->columns('IF(customer_id=0, subscriber_email, customer_id)');

            /** @var Varien_Db_Select $query */
            $innerQuery = new Varien_Db_Select($select1->getAdapter());
            $innerQuery->union(array($select1, $select2), Zend_Db_Select::SQL_UNION_ALL);

            $query = new Varien_Db_Select($select1->getAdapter());
            $query->from(array('subscriber_extract' => $innerQuery));
            $query->joinLeft($this->getTable('customer_entity'), 'subscriber_extract.customer_id = ' . $this->getTable('customer_entity') . '.entity_id');
            $query->joinLeft($this->getTable('newsletter_subscriber'), 'subscriber_extract.customer_id = ' . $this->getTable('newsletter_subscriber') . '.customer_id AND ' . $this->getTable('newsletter_subscriber') . '.customer_id != 0');
            $query->joinLeft(array($this->getTable('newsletter_subscriber') . '_guest' => $this->getTable('newsletter_subscriber')), 'subscriber_extract.customer_id = ' . $this->getTable('newsletter_subscriber') . '_guest' . '.subscriber_email AND ' . $this->getTable('newsletter_subscriber') . '_guest.customer_id = 0');

            $query->reset(Zend_Db_Select::COLUMNS);
            if ($count) {
                $query->columns('COUNT(DISTINCT subscriber_extract.customer_id) as total');
            } else {
                $query->columns('subscriber_extract.customer_id as customer_id')->distinct();
            }
        } else {
            /** @var Varien_Db_Select $query */
            $query = Mage::getModel('newsletter/subscriber')
                ->getCollection()
                ->addFieldToFilter('store_id', $storeId)
                ->getSelect();

            $query
                ->reset(Zend_Db_Select::COLUMNS)
                ->reset(Zend_Db_Select::FROM)
                ->reset(Zend_Db_Select::WHERE)
                ->from($this->getTable('newsletter_subscriber'), '')
                ->where($this->getTable('newsletter_subscriber') . '.store_id = ?', $storeId);

            if ($count) {
                $query
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns('COUNT(DISTINCT newsletter_subscriber.customer_id) as total');
            } else {
                $query->columns('IF(newsletter_subscriber.customer_id=0, subscriber_email, newsletter_subscriber.customer_id) AS customer_id')->distinct();;
            }
        }

        if ($differential !== false) {

            $date = new DateTime();
            $date->setTimestamp($differential);

            if ($exportWithCustomer) {
                $query->joinLeft($this->getTable('customer_address_entity'), 'subscriber_extract.customer_id = ' . $this->getTable('customer_address_entity') . '.parent_id', null);
                $query->joinLeft($this->getTable('dolist_dolistv8_calculatedfields'), 'subscriber_extract.customer_id = ' . $this->getTable('dolist_dolistv8_calculatedfields') . '.customer_id', null);
                $query->joinLeft($this->getTable('sales_flat_quote'), 'subscriber_extract.customer_id = ' . $this->getTable('sales_flat_quote') . '.customer_id', null);

                $query->where(
                    $this->getTable('customer_entity') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('customer_address_entity') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('dolist_dolistv8_calculatedfields') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('sales_flat_quote') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('newsletter_subscriber') . '.updated_at >= ' . $differential
                    . ' OR ' . $this->getTable('newsletter_subscriber') . '_guest.updated_at >= ' . $differential
                );
            }
            else {
                $query->joinLeft($this->getTable('customer_entity'), 'newsletter_subscriber.customer_id = ' . $this->getTable('customer_entity') . '.entity_id', null);
                $query->joinLeft($this->getTable('customer_address_entity'), 'newsletter_subscriber.customer_id = ' . $this->getTable('customer_address_entity') . '.parent_id', null);
                $query->joinLeft($this->getTable('dolist_dolistv8_calculatedfields'), 'newsletter_subscriber.customer_id = ' . $this->getTable('dolist_dolistv8_calculatedfields') . '.customer_id', null);
                $query->joinLeft($this->getTable('sales_flat_quote'), 'newsletter_subscriber.customer_id = ' . $this->getTable('sales_flat_quote') . '.customer_id', null);

                $query->where(
                    $this->getTable('customer_entity') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('customer_address_entity') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('dolist_dolistv8_calculatedfields') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('sales_flat_quote') . '.updated_at >= \'' . $date->format('Y-m-d\TH:i:sP') . '\''
                    . ' OR ' . $this->getTable('newsletter_subscriber') . '.updated_at >= ' . $differential
                );
            }

        }

        $query->limitPage($startPagination, $exportPageSize);

        return $query;
    }



    /**
     * Retrieve export file name, either stored value or generated by webservice
     *
     * @param string $exportType Export type. Can be 'full_export'|'differential_export'
     * @param int $storeId Store ID
     *
     * @return string Export filename
     */
    public function retrieveFileName($exportType, $storeId)
    {
        $flagCode = 'dolist_' . $exportType . '_filename_' . $storeId;

        // First, try to retrieve stored value
        $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');

        // Export file name already set => just return value
        if ($flag->getId() != null) {
            $exportFileName = $flag->getFlagData();
        } else {
            // If there is no stored value, retrieve it from webservice call
            /** @var Mage_Core_Model_Store $store */
            $store = Mage::getModel('core/store')->load($storeId);
            $storeName = sprintf('%s - %s', $store->getWebsite()->getName(), $store->getName());

            switch ($exportType) {
                case 'full_export':
                    $fileName = self::MAGENTO_FULL_EXPORT_LABEL . '(' . $storeName . ')';
                    break;

                case 'differential_export':
                    $fileName = self::MAGENTO_DIFF_EXPORT_LABEL . '(' . $storeName . ')';
                    break;
            }
            $response = $this->dolistV8CreateImport($fileName, false, $storeId);
            $exportFileName = $response->getData('FileName');

            // Then store it for next time
            $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
            $flag->setFlagData($exportFileName)
                ->save();
        }

        return $exportFileName;
    }

    /**
     * Generate export file
     * Can be full or differential export
     *
     * @param string $exportType Export type. Can be 'full_export'|'differential_export'
     * @param string $exportFileName Export filename
     * @param int $storeId Sto re ID
     *
     * @param int $startPagination
     * @return string Tmp local file name
     */
    public function generateExportFile($exportType, $exportFileName, $storeId, $startPagination = 1)
    {
        $localFilename = null;
        $error = false;
        $exportPageSize = Mage::getStoreConfig('dolist/dolist_v8/export_page_size');

        if (!$exportPageSize || intval($exportPageSize) < 1) {
            $exportPageSize = 5000;
        }

        $report = null;
        $reportId = $this->_getHelper()->getCurrentReportId($exportType, $storeId);

        $this->_getHelper()->logDebug('Report : ' . $reportId);
        if ($reportId) {
            /** @var Dolist_Net_Model_Reports $report */
            $report = Mage::getModel('dolist/reports')->load($reportId);
        }

        $this->_getHelper()->logDebug('ExportType : ' . $exportType);
        // If differential export, filter subscribers modified from last export
        if ($exportType == 'differential_export') {

            // Look for last export date
            // First, try to retrieve stored value
            $flag = $this->_getHelper()->getFlagDolistLastExport($storeId);

            // Log error if flag has never been set
            if ($flag->getId() == null) {
                $this->_getHelper()->logError($this->_getHelper()->__('At least one full export should have been perfomed before generating differential export file.'));
                $report->log($this->_getHelper()->__('At least one full export should have been perfomed before generating differential export file.'));
                $report->end('error');

                // Stop processing if export never performed
                $error = true;

            } else {

                $lastExportTimestamp = $flag->getFlagData(); // timestamp format
                $query = $this->getCustomerIdCollection($startPagination, $storeId, false, $lastExportTimestamp);
            }
        }

        if (!isset($query) || !$query) {
            $query = $this->getCustomerIdCollection($startPagination, $storeId, false);
        }

        if (Mage::getStoreConfig('dev/log/active') && $report) {
            $report->log($query->assemble());
        }

        $collection = $query->query()->fetchAll();

        // Perform only if collection is not empty and no error found
        if (count($collection) > 0 && !$error) {
            $startPagination++;
            $this->_getHelper()->addExportPaginationStart($exportType, $startPagination, $storeId);

            /** @var Dolist_Net_Model_Exporter_Csv $exporter */
            $exporter = Mage::getModel('dolist/exporter_csv');
            $exporter->setEnclosure(); // Dolist does not use enclosures
            $exporter->setDelimiter(chr(9));

            $i = 0;
            // Write one line for each subscriber to export
            foreach ($collection as $object) {
                $i++;

                if ($i % 100 == 0) {
                    if ($report) {
                        $report->progress(($startPagination - 2) * $exportPageSize + $i);
                    }
                }

                $localFilename = sys_get_temp_dir() . '/' . $exportFileName; // Temporary filename

                // Write in tmp CSV file
                $exporter->export($localFilename, $object, 'dolist', 'auto', true, $storeId);

                //We reset until all content is exported
                $localFilename = null;
            }

            $report->progress(($startPagination - 2) * $exportPageSize + $i);
            Mage::log('Exporting ' . count($collection) . ' items to ' . sys_get_temp_dir() . '/' . $exportFileName);
            $this->_getHelper()->logError('Exporting ' . count($collection) . ' items to ' . sys_get_temp_dir() . '/' . $exportFileName);
        } else {
            $this->_getHelper()->addExportPaginationStart($exportType, 0, $storeId);
        }

        /* get total count */
        $count = $this->_getHelper()->getExportTotalCount($exportType, $storeId);
        $lastPageNumber = ceil($count / $exportPageSize);
        $collectionSize = count($collection);
        if ($startPagination > $lastPageNumber && $collectionSize > 0) {
            //ready to export
            $localFilename = sys_get_temp_dir() . '/' . $exportFileName; // Temporary filename
            $this->_getHelper()->addExportPaginationStart($exportType, 0, $storeId);
        } elseif ($startPagination <= $lastPageNumber && $collectionSize > 0) {
            //Need another iteration
            Mage::register('pause_export', true);
            $this->_getHelper()->addExportStoreId($exportType, $storeId);
        }
        if ($startPagination > $lastPageNumber) {
            $this->_getHelper()->addExportPaginationStart($exportType, 0, $storeId);
        }

        return $localFilename;
    }

    /**
     * Generate segment file
     * File format:
     * - file at UTF-8 format
     * - separator: carriage return
     * - Header line: Email
     * - only email address per line
     *
     * @param mixed $collection Collection of customers (Mage_Customer_Model_Customer) to export
     * @param string $exportFileName Export filename
     *
     * @return string Tmp segment file name
     */
    public function generateSegmentFile($collection, $exportFileName, $report = null)
    {
        $localFilename = null;

        // Perform only if collection is not empty
        if ($collection->getSize() > 0) {
            /** @var Dolist_Net_Model_Exporter_Csv $exporter */
            $exporter = Mage::getModel('dolist/exporter_csv');
            $exporter->setEnclosure(); // Dolist does not use enclosures
            $exporter->setDelimiter(chr(9));

            // Write one line for each customer to export
            $i = 0;
            foreach ($collection as $customer) {

                $localFilename = sys_get_temp_dir() . '/' . $exportFileName; // Temporary filename

                // Write in tmp CSV file
                $exportObj = array(
                    'prefix' => '',
                    'object' => $customer,
                    'fields' => array(
                        'email'
                    )
                );
                $exporter->export($localFilename, $exportObj, 'auto', 'auto', true);

                if($i && $i%100 === 0)
                if($report instanceof Dolist_Net_Model_Reports) {
                    $report->progress(100);
                    $report->log('100 lines added...');
                }
                $i++;
            }
        }

        return $localFilename;
    }

    /**
     * Push export file to FTP server
     *
     * @param string $exportFileName Export filename
     * @param string $localFilename Local export filename (usually in /tmp directory)
     * @param int $storeId Store ID
     *
     * @param null $report
     * @return boolean
     */
    public function pushExportFile($exportFileName, $localFilename, $storeId, $report = null)
    {
        $this->_getHelper()->logDebug('Start push file to dolist');

        // terminate current cron iteration
        Mage::register('pause_export', true, true);

        /** @var Dolist_Net_Model_Reports $report */
        if ($report instanceof Dolist_Net_Model_Reports) {
            $report->log(sprintf('EXPORT for store %s', $storeId));
            $report->log(sprintf('local filename %s', $localFilename));
            $report->log(sprintf('FTP filename %s', $exportFileName));
        }

        //reencode file in UTF16 with BOM
        $this->_reencodeFileToExport($localFilename);

        // Zip file
        $zipFilename = str_replace('.txt', '.zip', $localFilename);
        if ($report instanceof Dolist_Net_Model_Reports) {
            $report->log(sprintf('zip filename %s', $zipFilename));
        }
        $zip = new ZipArchive();
        $zip->open($zipFilename, ZipArchive::CREATE);
        $zip->addFile($localFilename, basename($localFilename));
        $zip->close();

        /** @var Dolist_Net_Model_Dolistv8_Ftp $ftpConnection */
        $ftpConnection = Mage::getModel('dolist/dolistv8_ftp', array('store_id' => $storeId));
        $connection = $ftpConnection->getConnection();
        $result = false;

        try {
            // Generate remote filename from export filename
            $remoteFolder = (string)Mage::getConfig()->getNode('dolistparams/ftp/upload_contact_directory');
            // To be sure to get only one slash
            $remoteFolder = rtrim($remoteFolder, '/') . '/';
            $remoteFilename = $remoteFolder . basename($zipFilename);

            if ($report instanceof Dolist_Net_Model_Reports) {
                $report->log(sprintf('Remote filename %s', $remoteFilename));
            }

            // Push on FTP server
            $result = $connection->write($remoteFilename, $zipFilename);

            // Close FTP connection
            $connection->close();

            // Delete tmp file
            unlink($localFilename);
            unlink($zipFilename);

        } catch (Exception $e) {
            // Delete tmp file
            unlink($localFilename);
            unlink($zipFilename);

            $report->log($e->getMessage());
            $this->_getHelper()->logError($e->getMessage());
        }

        return $result;
    }

    /**
     * Perfom segment export for given $segmentId to Dolist-V8
     * Perform one export for each website.
     * Websites have been filtered before to avoid several sends with same Dolist-V8 configuration
     *
     * @param int $segmentId Customer segment Id
     * @param array $websiteIds Websites where Dolist-V8 is enabled
     * @param array $storeIds Websites default store where Dolist-V8 is enabled. Array is indexed by website id
     *
     * @return bool Return true if export is successful, false otherwise
     */
    public function exportSegment($segmentId, $websiteIds, $storeIds)
    {
        $return = true;

        /** @var Enterprise_CustomerSegment_Model_Segment $customerSegment */
        $customerSegment = Mage::getModel('enterprise_customersegment/segment')->load($segmentId);
        $exportPerformed = false;

        // One export performed for each website
        foreach ($websiteIds as $configWebsiteId => $websiteListToFilter) {

            $storeId = $storeIds[$configWebsiteId];

            $websiteNames = array();
            if(is_array($websiteListToFilter)) {
                foreach($websiteListToFilter as $websiteId) {
                    $websiteNames[] = Mage::app()->getWebsite($websiteId)->getName();
                }
            } else {
                $websiteNames[] = Mage::app()->getWebsite(reset($websiteIds))->getName();
            }

            /** @var Dolist_Net_Model_Reports $report */
            $report = Mage::getModel('dolist/reports');
            $report->setData(array(
                'type' => 'export',
                'name' => sprintf('Segment Export %s - (%s)',$customerSegment->getName(),join(',', $websiteNames ))
            ))->save();

            if ($customerSegment->getId()) {

                // Retrieve segment customers to export
                $collection = Mage::getResourceModel('enterprise_customersegment/report_customer_collection');
                $collection->addNameToSelect()
                    ->setViewMode($customerSegment->getViewMode())
                    ->addSegmentFilter($customerSegment)
                    ->addWebsiteFilter($websiteListToFilter);

                $report->start($collection->count());

                if ($collection->count() > 0) {

                    // Create new import.
                    $flagCode = 'dolist_segment_filename_' . $segmentId;

                    // First, try to retrieve stored value
                    /** @var Mage_Core_Model_Flag $flag */
                    $flag = Mage::getModel('core/flag')->load($flagCode, 'flag_code');

                    if ($flag->getId() != null) {
                        $exportFileName = $flag->getFlagData();
                    }
                    else {
                        // ImportName : 'MAGENTO - <segment.name> - <YYYYMMDD_HHmmss>
                        $now = new Zend_Date();
                        $importName = 'MAGENTO - ' . $customerSegment->getName() . ' - ' . $now->toString('YYYYMMdd_HHmmss');
                        $response = $this->dolistV8CreateImport($importName, true, $storeId);
                        $exportFileName = $response->getData('FileName');

                        $flag = Mage::getModel('core/flag', array('flag_code' => $flagCode));
                        $flag->setFlagData($exportFileName)->save();
                    }

                    // Create segment file
                    if (!is_null($exportFileName)) {
                        $localFilename = $this->generateSegmentFile($collection, $exportFileName, $report);

                        if (!is_null($localFilename)) {
                            // Push file on FTP server
                            $exportPerformed = $this->pushExportFile($exportFileName, $localFilename, $storeId, $report);
                        }
                    }

                    if ($exportPerformed) {
                        // If success, check if this segment has already been exported
                        // If never exported, add it to exported segment list (dolist_exported_segment_list)
                            foreach($websiteListToFilter as $websiteId) {
                                $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
                                $this->_getHelper()->addExportedSegment($customerSegment->getId(), $storeId);
                            }

                        Mage::getSingleton('adminhtml/session')
                            ->addSuccess($this->_getHelper()->__("Exporting segment id %s to Dolist-V8 succeeded", $segmentId));
                        $report->end('success');
                        $report->log(sprintf('Exporting segment id %s to Dolist-V8 succeeded', $segmentId));
                    } else {

                        Mage::getSingleton('adminhtml/session')
                            ->addError($this->_getHelper()->__("Exporting segment id %s to Dolist-V8 failed", $segmentId));

                        $report->end('error');
                        $report->log(sprintf('Exporting segment id %s to Dolist-V8 failed', $segmentId));

                        $return = false;
                    }

                } else {
                    Mage::getSingleton('adminhtml/session')
                        ->addError($this->_getHelper()->__("Segment id %s has not been exported because it is empty", $segmentId));
                    $report->end('error');
                    $report->log(sprintf('Segment id %s has not been exported because it is empty', $segmentId));

                    $return = false;
                }

            } else {
                Mage::getSingleton('adminhtml/session')
                    ->addNotice($this->_getHelper()->__("Segment id %s couldn't be loaded", $segmentId));

                $report->end('error');
                $report->log(sprintf('Segment id %s couldn\'t be loaded', $segmentId));

                // Remove this segment from dolist exported segment list
                $allStoreIds = array_keys(Mage::app()->getStores());
                // Remove exported segments from all stores
                foreach ($allStoreIds as $storeId) {
                    $this->_getHelper()->removeExportedSegment($segmentId, $storeId);
                }

                $report->delete();

            }
        }

        return $return;
    }

    /**
     * Log web service error
     *
     * @param Varien_Object $request Contain request
     * @param SoapFault $fault Web service exception response
     * @param string $method Web service method
     * @param string $additionalInfo Additional info for logs
     *
     * @return void
     */
    protected function _logError($request, $fault, $method, $additionalInfo = '')
    {
        Mage::helper('dolist/log')->logError($request, $fault, $method, $additionalInfo);
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
     * Reencode a file in UTF16 with BOM
     *
     * @param string $filename
     * @return string
     */
    protected function _reencodeFileToExport($filename)
    {
        $origContent = file_get_contents($filename);

        $encoding = mb_detect_encoding($origContent, "auto");
        // escape all of the question marks so we can remove artifacts from
        // the unicode conversion process
        $encodedContent = str_replace("?", "[question_mark]", $origContent);
        $encodedContent = mb_convert_encoding($encodedContent, "UTF-16LE", $encoding);
        $encodedContent = str_replace("?", "", $encodedContent);
        // replace the token string "[question_mark]" with the symbol "?"
        $encodedContent = str_replace("[question_mark]", "?", $encodedContent);
        // add BOM
        $encodedContent = "\xFF\xFE" . $encodedContent;

        file_put_contents($filename, $encodedContent);
        return $filename;
    }

    /**
     * Get table name (validated by db adapter) by table placeholder
     *
     * @param string|array $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        $cacheKey = $this->_getTableCacheName($tableName);
        if (!isset($this->_tables[$cacheKey])) {
            /** @var Mage_Core_Model_Resource $coreResource */
            $coreResource = Mage::getSingleton('core/resource');
            $this->_tables[$cacheKey] = $coreResource->getTableName($tableName);
        }
        return $this->_tables[$cacheKey];
    }

    /**
     * Retrieve table name for cache
     *
     * @param string|array $tableName
     * @return string
     */
    protected function _getTableCacheName($tableName)
    {
        if (is_array($tableName)) {
            return join('_', $tableName);

        }
        return $tableName;
    }
}
