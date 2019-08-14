<?php
/**
 * Dolist-EMT SOAP response for SendMail method
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Dolistemt_Response_SendEmail extends Dolist_Net_Model_Service_Message_Abstract
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
        $response = "";
        $listResult = $this->getData('SendEmailResult');
        if ($listResult != null) {
            $listResult = (array)$listResult;
            $response = $listResult['GetTemplateResponse'];
        }
        
        return $response;
    }
    
}