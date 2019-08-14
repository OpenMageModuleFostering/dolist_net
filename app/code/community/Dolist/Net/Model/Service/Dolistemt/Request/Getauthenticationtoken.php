<?php
/**
 * Dolist-EMT SOAP request for GetAuthenticationToken method
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2011 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistemt_Request_Getauthenticationtoken extends Dolist_Net_Model_Service_Message_Abstract
{
    /**
     * Intialize default values
     * 
     * @param array $params Init params
     * 
     * @return Dolist_Net_Model_Service_Dolistv8_Request_Getauthenticationtoken Request
     */
    public function __construct($params=null)
    {
        $storeId = 0;
        if (!is_null($params) && array_key_exists('store_id', $params)) {
            $storeId = $params['store_id'];
        }
        
        if (is_null($params['account_id'])) {
            $accountId = Mage::getStoreConfig(self::XML_DOLIST_EMT_ACCOUNTID, $storeId);
        } else {
            $accountId = $params['account_id'];
        }
        
        if (is_null($params['auth_key'])) {
            $authKey = Mage::getStoreConfig(self::XML_DOLIST_EMT_AUTHENTICATION_KEY, $storeId);
        } else {
            $authKey = $params['auth_key'];
        }
        
        // Init default values
        $this->setAuthenticationRequest(
            array(
                'AccountID'         => $accountId,
                'AuthenticationKey' => $authKey
            )
        );
        return $this;
    }
}