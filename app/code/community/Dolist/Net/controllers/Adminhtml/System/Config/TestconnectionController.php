<?php
/**
 * Admin Dolist test connection controller
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Adminhtml_System_Config_TestconnectionController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/dolist');
    }

    /**
     * Check connection to Dolist-V8 webservice,
     * then check connection to Dolist-V8 FTP server
     * 
     * @return void
     */
    public function pingdolistv8Action()
    {
        // Response is json
        $this->getResponse()->setHeader('Content-type', 'application/json');
        
        $accountid          = $_REQUEST['accountid'];
        $authenticationKey  = $_REQUEST['authentication_key'];
        $login              = $_REQUEST['login'];
        $password           = $_REQUEST['password'];
        $storeId            = $_REQUEST['storeId'];
        
        $error    = false;
        /** @var Dolist_Net_Model_Service $service */
        $service  = Mage::getModel('dolist/service');
        // First check connection to Dolist V-8 webservice
        $response = $service->dolistV8GetAuthenticationToken($storeId, $accountid, $authenticationKey);
        $key = $response->getData('Key');
        
        // OK if key is found and not empty
        if ($key != null && $key != "") {
            
            // Then check connection to FTP server
            $ftpConnection = Mage::getSingleton('dolist/dolistv8_ftp');
            // Arguments to check in live, just typed in back office
            $ftpConnection->fixArgs($login, $password);
            $connectionStatus = $ftpConnection->getStatus();
            if ($connectionStatus) {
            
                // result is true if authentication key is retrieved and FTP connection is OK
                $result['connectionStatus'] = 1;
                
            } else {
                $error = true;
            }

        } else {
            $error = true;
        }
        
        if ($error) {
            $errorDetail = Mage::getSingleton('core/session')->getLastDolistFaultDetail();
            
            // result is false if there is exception
            $result['connectionStatus'] = 0;
            $result['error'] = $errorDetail;
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    /**
     * Check if connection to Dolist-EMT webservice
     * and return template list to preload them
     * 
     * @return void
     */
    public function pingdolistemtAction()
    {
        // Response is json
        $this->getResponse()->setHeader('Content-type', 'application/json');
        
        $accountid          = $_REQUEST['accountid'];
        $authenticationKey  = $_REQUEST['authentication_key'];
        $storeId            = $_REQUEST['storeId'];
        
        $error    = false;
        $service  = Mage::getModel('dolist/service');
        // First check connection to Dolist EMT webservice
        $response = $service->dolistEmtGetAuthenticationToken($storeId, $accountid, $authenticationKey);
        $key = $response->getData('Key');
        // OK if key is found and not empty
        if ($key != null && $key != "") {
            $response = $service->dolistEmtGetTemplateList($storeId, $accountid, $authenticationKey);
            // result is true if authentication key is retrieved
            $result['connectionStatus'] = 1;
            
            // extract template response array
            $templateResponse = null;
            if ($response->getData('GetTemplateListResult')) {
                $templateResponse = $response->getData('GetTemplateListResult')->GetTemplateResponse;
            }
            
            // refresh templatelist
            if (!is_null($templateResponse)) {

                if (!is_array($templateResponse)) {
                    $templateResponse = array($templateResponse);
                }
    
                // Convert webservice response to Varien Object, more handable
                $templateList = array();
                foreach ($templateResponse as $templateData) {
                    $template = (array)$templateData;
                    $templateList[$template['ID']] = $template['ID'] . ' - ' . $template['Name'];
                }
                // Compare template list retrieved by webservice with stored one, and update it if necesary 
                $templateListUpdated = Mage::getModel('dolist/dolistemt_template')->update($templateList);
                
                // result is true if template list is loaded
                $result['connectionStatus']     = 1;
                // inform if template list need to be updated
                $result['templateListUpdated']  = $templateListUpdated;
                // provide template list
                $result['templateList']         = $templateList;
    
            } else {
                // result is false if there is exception
                $error = true;
            }
        } else {
            $error = true;
        }
        
        if ($error) {
            $errorDetail = Mage::getSingleton('core/session')->getLastDolistFaultDetail();
            
            // result is false if there is exception
            $result['connectionStatus'] = 0;
            $result['error'] = $errorDetail;
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
