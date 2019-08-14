<?php
/**
 * Dolist Helper
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Helper_Log extends Mage_Core_Helper_Abstract
{
    const LOGFILE = 'dolist.log';
    /**
     * Log web service error
     * 
     * @param Varien_Object $request        Contain request
     * @param SoapFault     $fault          Web service exception response
     * @param string        $method         Web service method
     * @param string        $additionalInfo Additional info for logs
     * 
     * @return void
     */
    public function logError($request, $fault, $method, $additionalInfo='')
    {
        Mage::log(
            $method . ' call to Dolist web service failed',
            Zend_Log::INFO,
            self::LOGFILE
        );
        Mage::log(
            'REQUEST',
            Zend_Log::INFO,
            self::LOGFILE
        );
        Mage::log(
            (array)$request,
            Zend_Log::INFO,
            self::LOGFILE
        );
        Mage::log(
            'RESPONSE',
            Zend_Log::INFO,
            self::LOGFILE
        );
        
        // Store fault detail in session to use it in controllers and/or in templates
        $fault = (array)$fault;
        if (array_key_exists('detail', $fault)) {
            $detailFault = (array)$fault['detail'];
            
            Mage::log(
                $detailFault,
                Zend_Log::INFO,
                self::LOGFILE
            );
            
            if (array_key_exists('ServiceException', $detailFault)) {
                Mage::getSingleton('core/session')->setLastDolistFaultDetail((array)$detailFault['ServiceException']);
            }
        }
        
        if ($additionalInfo != '') {
            Mage::log(
                (string)$additionalInfo,
                Zend_Log::INFO,
                self::LOGFILE
            );
        }
    }
}
