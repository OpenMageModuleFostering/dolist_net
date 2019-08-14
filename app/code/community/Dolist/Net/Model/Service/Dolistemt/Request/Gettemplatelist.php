<?php
/**
 * Dolist-EMT SOAP request for GetTemplateList method
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2011 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistemt_Request_Gettemplatelist extends Dolist_Net_Model_Service_Message_Abstract
{
    /**
     * Intialize default values
     * 
     * @param array $params Init parameters
     * 
     * @return Dolist_Net_Model_Service_Dolistemt_Request_Gettemplatelist
     */
    public function __construct($params=null)
    {
        // Init params
        $storeId = 0;
        if (array_key_exists('store_id', $params)) {
            $storeId = $params['store_id'];
        }
        
        if (is_null($params['account_id'])) {
            $accountId = Mage::getStoreConfig(self::XML_DOLIST_EMT_ACCOUNTID, $storeId);
        } else {
            $accountId = $params['account_id'];
        }
        
        $key = $params['key'];
        
        // Init default values
        $this->setToken(
            array(
                'AccountID' => $accountId,
                'Key'       => $key
            )
        );
        
        return $this;
    }
    
}