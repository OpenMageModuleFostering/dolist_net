<?php

/**
 * Exports a set of Varien Objects to a CSV file
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Exporter_Csv extends Varien_Object
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDelimiter(',');
        $this->setEnclosure('"');
        $this->setNoLineBreak(true);
    }

    /**
     * render all objects previously added to a CSV file
     *
     * @param string $filename Filename
     * @param $contact
     *
     * @param string $exportType
     * @param mixed $addHeader auto => see $append, false => do not add, true => allways add
     * @param boolean $append if set to true and the file already exists the content is added at the end of the file
     * and the headers are not added (they are allready at the beginning of the file)
     * @param int $storeId
     * @return $this
     */
    public function export($filename, $contact, $exportType = 'auto', $addHeader = 'auto', $append = true, $storeId = 0)
    {
        $varienCsv = new Varien_File_Csv();

        $row = null;
        if ($exportType == 'auto') {
            $row = $this->autoExportGetRow($contact);
        } elseif ($exportType == 'dolist') {
            $row = $this->dolistExportGetRow($storeId, $contact);
        }

        if (!$row) {
            return $this;
        }

        //open the file
        $mode = 'a';
        if (!$append) {
            $mode = 'w';
        }
        if ($addHeader == 'auto') {
            if (is_file($filename)) {
                $addHeader = false;
            } else {
                $addHeader = true;
            }
        }


        try {
            if (!is_dir(dirname($filename))) {
                mkdir(dirname($filename), 0755, true);
            }

            $file = fopen($filename, $mode);

            // Prepare header every time (needed to prepare cells even if empty)
            $header = array_keys($this->getMapping(Mage::app()->getRequest()->getParam('store', $storeId)));

            //  but add the header only if needed
            if ($addHeader) {
                $varienCsv->fputcsv($file, $header, $this->getDelimiter(), $this->getEnclosure());
            }

            // export file must contain as many columns as header
            $filledRow = array_fill(0, count($header), '');
            // fill the row with empty values for empty fields
            foreach (array_values($row) as $rowKey => $rowValue) {
                $filledRow[$rowKey] = $rowValue;
            }

            //add data
            $varienCsv->fputcsv($file, $filledRow, $this->getDelimiter(), $this->getEnclosure());

        } catch (Exception $e) {
            $this->_getHelper()->logDebug($e->__toString());
            Mage::throwException($e->getMessage());
        }
        try {

            fclose($file);

        } catch (Exception $e) {
            // file allready closed
        }

        return $this;
    }

    /**
     * Return row to export - automatic method
     *
     * @param $contact
     * @return array
     */
    protected function autoExportGetRow($contact)
    {
        $row = array();
        //fill the row

        //transform this array in local variables to have a more readable code
        $obj = $contact['object'];
        $prefix = "";
        if (isset($contact['prefix'])) {
            $prefix = $contact['prefix'];
        }
        $fields = false;
        if (isset($contact['fields'])) {
            $fields = $contact['fields'];
        }
        $exclude = false;
        if (!$fields && isset($contact['exclude'])) {
            $exclude = $contact['exclude'];
        }

        //check all fields
        if ($fields !== false) {
            // only some fields must be added

            foreach ($fields as $k) {
                $v = "-"; //avoid empty columns causing problems with the header
                if ($obj->hasData($k) && $obj->getData($k) != '') {
                    $v = $obj->getData($k);
                }
                $row[$prefix . $k] = $this->_checkValueBeforeInsert($v);
            }

        } else {
            foreach ($obj->getData() as $k => $v) {
                $add = true;

                if ($exclude !== false) {
                    //some fields musn't be exported
                    $add = !array_key_exists($k, $exclude);
                }

                if ($add) {
                    //add this field to the current row!
                    $row[$prefix . $k] = $this->_checkValueBeforeInsert($v);
                }
            }
        }

        return $row;
    }

    /**
     * Return row to export - dolist method
     *
     * @param $storeId
     * @param $contact
     * @return array
     */
    public function dolistExportGetRow($storeId, $contact)
    {
        $row = array();

        $mappingConfig = $this->getMapping($storeId);

        // If customer exists, load it
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = null;
        $forceAbonneNewsletter = false;

        /** @var Mage_Core_Model_Store $store */
        $store = Mage::getModel('core/store')->load($storeId);

        if (is_array($contact) && array_key_exists('customer_id', $contact) && filter_var($contact['customer_id'], FILTER_VALIDATE_INT) !== false) {
            $customer = Mage::getModel('customer/customer')->load($contact['customer_id']);
        } else {
            $customer = Mage::getModel('customer/customer');

            $customer->setWebsiteId($store->getWebsiteId());
            $customer->loadByEmail($contact['customer_id']);

            if (!$customer->getId()) {
                // If no customer exists, create a fictive customer (but DO NOT save it) with email, only in order to be exported
                $this->_getHelper()->logDebug('CustomerId : ' . $contact['customer_id']);
                $customer = Mage::getModel('customer/customer');
                $customer->setData('email', $contact['customer_id']);

                //
                $forceAbonneNewsletter = true;
            }
        }

        if (strlen($customer->getData('email')) == 0) {
            return null;
        }

        /** @var Dolist_Net_Model_Dolistv8_Calculatedfields $dolistCalculatedFieldsModel */
        $dolistCalculatedFieldsModel = Mage::getModel('dolist/dolistv8_calculatedfields');
        $dolistCalculatedFieldsModel->setStoreId($storeId);

        $config = Mage::getStoreConfig("dolist/dolist_v8/calculatedfieds_mode", $storeId);
        $configStartDate = ($config == Dolist_Net_Model_Dolistv8_Calculatedfields::BEGIN_DATE) ? Mage::getStoreConfig("dolist/dolist_v8/calculatedfieds_date", $storeId) : null;
        $computed = false;

        foreach ($mappingConfig as $dolistHeader => $magentoAttributeCode) {
            $v = '';
            /** @var Dolist_Net_Model_Exporter_Adapter_Default $adapter */
            $adapter = Mage::getModel($magentoAttributeCode['adapter']);

            if ($magentoAttributeCode['field'] == 'is_subscriber') {
                $subscriber = null;
                if (!$forceAbonneNewsletter) {
                    $subscriber = $this->_getHelper()->loadSubscriberByCustomer($customer, $storeId);
                }
                else {
                    $subscriber = $this->_getHelper()->loadSubscriberByEmail($contact['customer_id'], $storeId);

                }

                // 0 : not subscribed
                // 1 : subscribed
                // 2 : unsubscribed
                // 3 : dolist error
                if ($subscriber && ($forceAbonneNewsletter || ($subscriber instanceof Mage_Newsletter_Model_Subscriber && strlen($subscriber->getData('subscriber_status')) != 0))) {
                    switch($subscriber->getData('subscriber_status')) {
                        case 0:
                            $v = 0;
                            break;
                        case Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED:
                            $v = 1;
                            break;
                        case Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED:
                            $v = 2;
                            break;
                        default:
                            $v = 3;
                    }
                }
                else {
                    $v = 0;
                }

                $v = $adapter->getExportedValue($v);

            } else if ($customer->getId() && $dolistCalculatedFieldsModel->isCalculatedFieldConfig($magentoAttributeCode['field'])) {
                if (!$dolistCalculatedFieldsModel->getId()) {
                    $this->_getHelper()->logDebug(sprintf('Try to load calculatedfields for customerId %s and store %s', $customer->getId(), $storeId));
                    $dolistCalculatedFieldsModel->loadByCustomerId($customer->getId());
                }

                if (!$dolistCalculatedFieldsModel->getId()) {
                    $this->_getHelper()->logDebug('Missing calculatedfields. Create it');
                    $dolistCalculatedFieldsModel->compute();
                    $computed = true;
                } elseif ($dolistCalculatedFieldsModel->getData('order_expire') && ($dolistCalculatedFieldsModel->getData('orders_expire')) <= time()) {
                    if (!$computed) {
                        $this->_getHelper()->logDebug('Calculatedfield is expired. Refresh it');
                        $dolistCalculatedFieldsModel->compute();
                        $computed = true;
                    }
                } elseif ((!$dolistCalculatedFieldsModel->getData('first_order_amount') || $dolistCalculatedFieldsModel->getData('first_order_amount') == 0 || !$dolistCalculatedFieldsModel->getData('last_order_amount') || strlen($dolistCalculatedFieldsModel->getData('first_order_date')) > 0)) {
                    if (!$computed) {
                        $this->_getHelper()->logDebug('Calculatedfield is empty. Refresh it');
                        $dolistCalculatedFieldsModel->compute();
                        $computed = true;
                    }
                } elseif ($dolistCalculatedFieldsModel->getData('config') != $config || $dolistCalculatedFieldsModel->getData('start_date') != $configStartDate) {
                    // Calculated fields config has changed
                    if (!$computed) {
                        $this->_getHelper()->logDebug('Calculatedfield config has changed. Refresh it');
                        $dolistCalculatedFieldsModel->compute();
                        $computed = true;
                    }
                } elseif (strpos($magentoAttributeCode['field'], 'last_unordered_cart_') === 0) {
                    // Always
                    if (!$computed) {
                        $this->_getHelper()->logDebug('Unordered cart data used. Refresh calculatedfield');
                        $dolistCalculatedFieldsModel->compute();
                        $computed = true;
                    }
                }


                if ($magentoAttributeCode['field'] == 'avg_order_amount_excl_tax') {
                    $v = $adapter->getExportedValue($dolistCalculatedFieldsModel->getAvgOrdersAmount());
                } elseif ($magentoAttributeCode['field'] == 'avg_order_amount_incl_tax') {
                    $v = $adapter->getExportedValue($dolistCalculatedFieldsModel->getAvgOrdersAmount(true));
                } elseif ($magentoAttributeCode['field'] == 'avg_nb_products_per_order') {
                    $v = $adapter->getExportedValue($dolistCalculatedFieldsModel->getAvgProductCount());
                } else {
                    // Calculated fields
                    $v = $adapter->getExportedValue($dolistCalculatedFieldsModel->getData($magentoAttributeCode['field']));
                }

            } elseif (in_array($magentoAttributeCode['field'], array('company', 'address1', 'address2', 'address3', 'city', 'countryid', 'zipcode', 'postcode', 'fax', 'phone'))) {
                $address = $customer->getDefaultBillingAddress();
                /** @var Dolist_Net_Model_Exporter_Adapter_Default $adapter */
                $adapter = Mage::getModel($magentoAttributeCode['adapter']);

                if (!empty($address)) {

                    switch ($magentoAttributeCode['field']) {
                        case 'countryid':
                            $customerCountry = $address->getCountry();
                            if ($customerCountry != '') {
                                $countries = $this->_getCountries();
                                if (array_key_exists($customerCountry, $countries)) {
                                    /** @var Mage_Directory_Model_Country $country */
                                    $country = $countries[$customerCountry];
                                    $v = $adapter->getExportedValue($country->getDolistCode());
                                } else {
                                    $v = $adapter->getExportedValue(Dolist_Net_Helper_Data::OTHER_COUNTRIES_CODE);
                                }
                            } else {
                                $v = $adapter->getExportedValue('');
                            }
                            break;
                        case 'address1':
                            $v = $adapter->getExportedValue($address->getStreet(1));
                            break;
                        case 'address2':
                            $v = $adapter->getExportedValue($address->getStreet(2));
                            break;
                        case 'address3':
                            $v = $adapter->getExportedValue($address->getStreet(3));
                            break;
                        case 'phone':
                            $v = $adapter->getExportedValue($address->getData('telephone'));
                            break;
                        case 'zipcode':
                            $v = $adapter->getExportedValue($address->getData('zipcode'));
                            if (!$v || strlen($v)) {
                                $v = $adapter->getExportedValue($address->getData('postcode'));
                            }
                            break;
                        case 'postcode':
                            $v = $adapter->getExportedValue($address->getData('zipcode'));
                            if (!$v || strlen($v)) {
                                $v = $adapter->getExportedValue($address->getData('postcode'));
                            }
                            break;
                        default:
                            $v = $adapter->getExportedValue($address->getData($magentoAttributeCode['field']));
                            break;
                    }
                } else {
                    $v = $adapter->getExportedValue('');
                }

            } else {
                $adapter = Mage::getModel($magentoAttributeCode['adapter']);
                $v = $adapter->getExportedValue($customer->getData($magentoAttributeCode['field']));
            }

            //$adapter->getExportedValue($customer, $magentoAttributeCode);
            $row[$dolistHeader] = $this->_checkValueBeforeInsert($v);
        }

        return $row;
    }

    /**
     * @param $storeId
     * @return array
     */
    protected function getMapping($storeId)
    {
        /** @var Dolist_Net_Model_Dolistv8_Customfields $model */
        $model = Mage::getModel('dolist/dolistv8_customfields');
        return $model->getMapping($storeId);
    }

    /**
     * Check a value before inserting it into a CSV file
     *
     * checks:
     * Removes line breaks if $this->getNoLineBreak() is true
     *
     * @param string $value Value
     *
     * @return string
     */
    protected function _checkValueBeforeInsert($value)
    {
        if ($this->getNoLineBreak()) {
            $value = str_replace('\r', '', $value);
            $value = str_replace('\n', ' ', $value);
        }
        return $value;
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
     * Load countries
     *
     * @return array Countries
     */
    protected function _getCountries()
    {
        if (is_null($this->_countries)) {
            $countries = Mage::getModel('directory/country')
                ->getCollection()
                ->getItems();

            $this->_countries = $countries;
        }

        return $this->_countries;
    }
}