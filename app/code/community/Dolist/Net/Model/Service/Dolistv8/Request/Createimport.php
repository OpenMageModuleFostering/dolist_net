<?php
/**
 * Dolist-V8 SOAP request for CreateImport method
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2011 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistv8_Request_Createimport extends Dolist_Net_Model_Service_Message_Abstract
{
    /**
     * Intialize request
     * Key and ImportName need to be set
     * param string $key Authentication token key
     * param string $importName    Import name: "MAGENTO - Chargement complet" or "MAGENTO - Chargement différentiel"
     * param bool   $createSegment Does this import create segment or not. Default: false
     * 
     * @param array $params Init parameters
     * 
     * @return Dolist_Net_Model_Service_Dolistv8_Request_Createimport Request
     */
    public function __construct($params=null)
    {
        // Init params
        $storeId = 0;
        if (array_key_exists('store_id', $params)) {
            $storeId = $params['store_id'];
        }
        $key            = $params['key'];
        $importName     = $params['import_name'];
        $createSegment  = false;
        if (array_key_exists('create_segment', $params)) {
            $createSegment = $params['create_segment'];
        }
        
        // Init default values
        $this->setToken(
            array(
                'AccountID' => Mage::getStoreConfig(self::XML_DOLIST_V8_ACCOUNTID, $storeId),
                'Key'       => $key
            )
        )->setImportFile(
            array(
                'CreateSegment'     => $createSegment,
                'ImportName'        => $importName,
                'InterestCenter'    => '',
                'IsRent'            => false,
                'ProviderFileName'  => '',
                'RentCredit'        => 0,
                'ReportAddresses'   => array(Mage::getStoreConfig('trans_email/ident_general/email', $storeId)),
                'UpdateContacts'    => true
            )
        );
        
        return $this;
    }
}