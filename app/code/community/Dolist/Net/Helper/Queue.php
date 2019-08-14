<?php
/**
 * Dolist Helper
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Helper_Queue extends Mage_Core_Helper_Abstract
{
    const TEMPORARY_ERRORS_XML_KEY      = 'dolistparams/temporary_errors';
    const LIMITREACHED_ERRORS_XML_KEY   = 'dolistparams/limitreached_errors';
    
    protected $_temporaryErrors = null;
    protected $_limitReachedErrors = null;
    
    /**
     * Is the template is already queued
     * 
     * @param int $dolistTemplateId 
     * @return bool
     */
    public function isTemplateQueued($dolistTemplateId)
    {
        $collection = Mage::getModel('dolist/dolistemt_message_queued')
                        ->getCollection()
                        ->addFieldToFilter('template_id', $dolistTemplateId);
        return ($collection->count() > 0);
    }
    
    /**
     * Store the message to be processed later
     * 
     * @param int $dolistTemplateId
     * @param array|string $message
     * @param int $storeId
     */
    public function queueMessage($dolistTemplateId, $message, $storeId = 0)
    {
        if (!is_string($message)) {
            $message = serialize($message);
        }
        Mage::getModel('dolist/dolistemt_message_queued')
            ->load(null)
            ->setTemplateId($dolistTemplateId)
            ->setSerializedMessage($message)
            ->setStoreId($storeId)
            ->save();
    }
    
    /**
     * test if the SaopFault is a "template sending limit reached" one
     * 
     * @param SoapFault $fault
     * @return bool
     */
    public function isLimitReachedError(SoapFault $fault)
    {
        $faultDetail = $fault->detail;
        return in_array($faultDetail->ServiceException->ErrorCode, $this->_getLimitReachedErrors());
    }
    
    /**
     * test if the SaopFault is a temporary error (API call can be redo later)
     * 
     * @param SoapFault $fault
     * @return bool
     */
    public function isTemporaryError(SoapFault $fault)
    {
        $faultDetail = $fault->detail;
        return in_array($faultDetail->ServiceException->ErrorCode, $this->_getTemporaryErrors());
    }
    
    /**
     * get temporary error labels from config
     * 
     * return array<string>
     */
    protected function _getTemporaryErrors()
    {
        if (is_null($this->_temporaryErrors)) {
            $config = (string) Mage::getConfig()->getNode(self::TEMPORARY_ERRORS_XML_KEY);
            $this->_temporaryErrors = explode(',', $config);
        }
        return $this->_temporaryErrors;
    }
    
    /**
     * get "limit reached" error labels from config
     * 
     * return array<string>
     */
    protected function _getLimitReachedErrors()
    {
        if (is_null($this->_limitReachedErrors)) {
            $config = (string) Mage::getConfig()->getNode(self::LIMITREACHED_ERRORS_XML_KEY);
            $this->_limitReachedErrors = explode(',', $config);
        }
        return $this->_limitReachedErrors;
    }
}