<?php
/**
 * Attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Country extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    protected $_countries = null;
    
    /**
     * Synchronize data in real tables in production database from temporary tables
     * 
     * @param Mage_Customer_Model_Customer $customer Current customer
     * @param Varien_Object                $config   Config for this attribute
     * 
     * @return
     */
    public function getExportedValue($customer, $config)
    {
        $return             = '';
        $customerCountry    = '';
        
        $address = $customer->getDefaultBillingAddress();
        if (!empty($address)) {
            $customerCountry = $address->getCountry();
        }
        
        if ($customerCountry != '') {
            $countries = $this->_getCountries();
            if (array_key_exists($customerCountry, $countries)) {
                $country = $countries[$customerCountry];
                $return  = $country->getDolistCode();
            } else {
                $return = Dolist_Net_Helper_Data::OTHER_COUNTRIES_CODE;
            }
        }
        
        return $return;
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