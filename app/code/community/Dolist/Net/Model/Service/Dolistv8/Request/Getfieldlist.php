<?php
/**
 * Dolist-V8 SOAP request for GetFieldList method
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2011 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistv8_Request_Getfieldlist extends Dolist_Net_Model_Service_Message_Abstract
{
    /**
     * Intialize request
     * Key need to be set
     *
     * @param array $params Init params
     *
     * @return Dolist_Net_Model_Service_Dolistv8_Request_Getfieldlist Request
     */
    public function __construct($params=null)
    {
        // Init params
        $storeId = 0;
        if (!is_null($params) && array_key_exists('store_id', $params)) {
            $storeId = $params['store_id'];
        }
        $key = $params['key'];

        // Init default values
        $this->setToken(
            array(
                'AccountID' => Mage::getStoreConfig(self::XML_DOLIST_V8_ACCOUNTID, $storeId),
                'Key'       => $key
            )
        )->setRequest(
                array(
                    //'Culture' => 'fr-FR',
                    //'UserEmail'          => false,
                    //'ProfileID'  => 1,
                    //'Offset'      => array('Email', 'OptoutEmail'),
                    //'Limit'
                )
            );

        return $this;
    }
}