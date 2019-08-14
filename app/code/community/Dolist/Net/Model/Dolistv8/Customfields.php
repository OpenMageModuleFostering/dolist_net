<?php


class Dolist_Net_Model_Dolistv8_Customfields extends Mage_Core_Model_Abstract
{
    public static $coreFieldName = array(
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'birthdate' => 'dob',
        'company' => 'company',
        'address1' => 'address1',
        'address2' => 'address2',
        'address3' => 'address3',
        'zipcode' => 'zipcode',
        'city' => 'city',
        'countryid' => 'countryid',
        'phone' => 'phone',
        'fax' => 'fax',
    );

    protected function _construct()
    {
        parent::_construct();
        $this->_init('dolist/dolistv8_customfields');
    }

    public function getMapping($storeId = 0)
    {
        $mapping = array(
            'email' => array(
                'field' => 'email',
                'adapter' => 'dolist/exporter_adapter_default'
            )
        );

        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter('scope_id', array('eq' => $storeId));

        if($collection->count() == 0) {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('scope_id', array('eq' => 0));
        }

        foreach ($collection as $customField) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */

            $magentoField = $customField->getData('magento_field');
            if (!$magentoField) {
                // field not mapped
                continue;
            }

            $mapping[$customField->getData('name')] = array(
                'field' => $magentoField,
                'adapter' => $customField->getFieldAdapater()
            );
        }
        return $mapping;
    }

    public function loadByNameAndScope($name, $scopeId)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
        $collection = $this->getCollection()
            ->addFieldToFilter('name', $name)
            ->addFieldToFilter('scope_id', $scopeId);


        return $collection->getFirstItem();

    }

    public function toOptionArray($type, $storeId)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter('type', array('like' => $type));
        $collection->addFieldToFilter('scope_id', $storeId);

        $optionArray = array(
            array('value' => '', 'label' => Mage::helper('adminhtml')->__('Disable'))

        );

        foreach ($collection as $customField) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */

            $optionArray[] = array(
                'value' => $customField->getData('name'),
                'label' => $customField->getData('title')
            );
        }

        return $optionArray;
    }

    public function getFieldAdapater()
    {
        switch (strtolower($this->getData('type'))) {
            case 'integer':
                return 'dolist/exporter_adapter_int';
            case 'datetime':
                return 'dolist/exporter_adapter_date';
            case 'varchar':
            default:
                return 'dolist/exporter_adapter_default';
        }
    }
} 