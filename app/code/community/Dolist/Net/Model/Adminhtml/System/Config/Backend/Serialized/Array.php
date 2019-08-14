<?php
/**
 * Validate Dolist module system configuration
 * Used to add button in Back Office without massive rewrites
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Adminhtml_System_Config_Backend_Serialized_Array extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    /**
     * Validate configuration
     *
     * @return Dolist_Net_Model_System_Config_Check
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        // Retrieve data set by admin user
        $configData              = $this->getData();
        $groupValue              = null;
        $customStrFieldsValues   = null;
        $customIntFieldsValues   = null;
        $customDateFieldsValues  = null;
        
        $storeConfig = Mage::getStoreConfig('dolist/dolist_v8', $configData['scope_id']);
        
        if (array_key_exists('inherit', $configData['groups']['dolist_v8']['fields']['group'])) {
            $groupValue = $storeConfig['group'];
        } else {
            $groupValue = $configData['groups']['dolist_v8']['fields']['group']['value'];
        }
        
        if (array_key_exists('inherit', $configData['groups']['dolist_v8']['fields']['custom_str_fields'])) {
            $customStrFieldsValues = unserialize($storeConfig['custom_str_fields']);
        } else {
            $customStrFieldsValues = $configData['groups']['dolist_v8']['fields']['custom_str_fields']['value'];
        } 
        
        if (array_key_exists('inherit', $configData['groups']['dolist_v8']['fields']['custom_int_fields'])) {
            $customIntFieldsValues = unserialize($storeConfig['custom_int_fields']);
        } else {
            $customIntFieldsValues = $configData['groups']['dolist_v8']['fields']['custom_int_fields']['value'];
        }
        
        if (array_key_exists('inherit', $configData['groups']['dolist_v8']['fields']['custom_date_fields'])) {
            $customDateFieldsValues = unserialize($storeConfig['custom_date_fields']);
        } else {
            $customDateFieldsValues = $configData['groups']['dolist_v8']['fields']['custom_date_fields']['value'];
        }
         
        $integrityError = false;
        $setCustomStrFields  = array();
        $setCustomIntFields  = array();
        $setCustomDateFields = array();

        $setCustomStrFields[] = $groupValue;
        foreach ($customStrFieldsValues as $customStrFieldsValue) {

            // If data already found, cannot be used twice, so throw error
            if (is_array($customStrFieldsValue)) {
                if (in_array($customStrFieldsValue['dolist_custom_str_fields'], $setCustomStrFields)) {
                    $integrityError = true;
                    break;
                } else {
                    // Add data in array
                    $setCustomStrFields[] = $customStrFieldsValue['dolist_custom_str_fields'];
                }
            }
        }

        if (!$integrityError) {
            foreach ($customIntFieldsValues as $customIntFieldsValue) {

                // If data already found, cannot be used twice, so throw error
                if (is_array($customIntFieldsValue)) {
                    if (in_array($customIntFieldsValue['dolist_custom_int_fields'], $setCustomIntFields)) {
                        $integrityError = true;
                        break;
                    } else {
                        // Add data in array
                        $setCustomIntFields[] = $customIntFieldsValue['dolist_custom_int_fields'];
                    }
                }
            }

            if (!$integrityError) {
                foreach ($customDateFieldsValues as $customDateFieldsValue) {

                    // If data already found, cannot be used twice, so throw error
                    if (is_array($customDateFieldsValue)) {
                        if (in_array($customDateFieldsValue['dolist_custom_date_fields'], $setCustomDateFields)) {
                            $integrityError = true;
                            break;
                        } else {
                            // Add data in array
                            $setCustomDateFields[] = $customDateFieldsValue['dolist_custom_date_fields'];
                        }
                    }
                }
            }
        }

        // Display error message if integrity error
        if ($integrityError) {
            Mage::throwException(
                Mage::helper('dolist')
                ->__(
                    'Double check that group, custom str fields, custom int field and ' . 
                    'custom date fields do not share same value.'
                )
            );
        }
        
        parent::_beforeSave();
    }
}
