<?php
/**
 * Attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Address2 extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * Synchronize data in real tables in production database from temporary tables
     *
     * @param Mage_Customer_Model_Customer $customer Current customer
     * @param Varien_Object $config Config for this attribute
     *
     * @return string
     */
    public function getExportedValue($customer, $config)
    {
        $return = '';
        $address = $customer->getDefaultBillingAddress();
        if (!empty($address)) {
            $return = $address->getStreet(2);
        }
        return $return;
    }
}