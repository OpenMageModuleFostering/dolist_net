<?php
/**
 * Dolist-EMT SOAP request for SendEmail method
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2011 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistemt_Request_SendEmail extends Dolist_Net_Model_Service_Message_Abstract
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
        $key = $params['key'];
        $message = $params['message'];
        // Init default values
        $this->setToken(
            array(
                'AccountID' => Mage::getStoreConfig(self::XML_DOLIST_EMT_ACCOUNTID, $storeId),
                'Key'       => $key
            )
        )->setMessage($message);
        
        return $this;
    }
    
}