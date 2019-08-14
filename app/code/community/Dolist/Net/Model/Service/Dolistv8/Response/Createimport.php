<?php
/**
 * Dolist-V8 SOAP response for CreateImport request
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistv8_Response_Createimport extends Dolist_Net_Model_Service_Message_Abstract
{
    /**
     * Check web service response and return array result
     * 
     * @param array $arr Data to add to response
     * 
     * @return array 
     */
    public function addData(array $arr)
    {
        parent::addData($arr);
        
        // Check if there is result
        $response = null;
        $importReport = $this->getData('CreateImportResult');
        
        if ($importReport != null) {
            $response = Mage::getModel('dolist/service_message_abstract');
            $response->addData((array)$importReport);
        }
        
        return $response;
    }
    
}