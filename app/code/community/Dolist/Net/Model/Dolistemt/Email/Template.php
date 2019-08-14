<?php
/**
 * Rewrite Magento native email model template to send mail using Dolist-EMT webservice instead of default mail server
 * Only "send" method is modified
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Dolistemt_Email_Template extends Mage_Core_Model_Email_Template
{
    const XML_DOLIST_EMT_DEFAULT_TEMPLATE = 'dolist/dolist_emt/default_template';
    const XML_DOLIST_EMT_TEMPLATE_MAPPING = 'dolist/dolist_emt/template_mapping';
    
    /**
     * Send mail to recipient
     *
     * @param array|string      $email     E-mail(s)
     * @param array|string|null $name      receiver name(s)
     * @param array             $variables template variables
     * 
     * @return boolean
     **/
    public function send($email, $name = null, array $variables = array())
    {
        // To know scope of this send => use correct Dolist-EMT configuration scope
        $storeId = $this->getDesignConfig()->getStore();
        
        // Use native method if service is not enabled
        if (!Mage::helper('dolist')->isDolistEmtEnabled($storeId)) {
            return parent::send($email, $name, $variables);
        }
        
        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }
        
        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }
        
        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);
        $senderEmail = $this->getSenderEmail();
        $senderName = $this->getSenderName();
        $magentoTemplateId = $this->getTemplateId();
        $templateId = $this->_getDolistEmtTemplateId($magentoTemplateId);

        if((string)$templateId == '-1') {
            // EMT is disabled for this template
            return parent::send($email, $name, $variables);
        }

        // Build message
        $message = $this->_generateSendMessageRequestData(
            $senderEmail,
            $senderName,
            $text, // Email content
            $returnPathEmail,
            '',
            $this->getProcessedTemplateSubject($variables), // Subject
            $emails, // Recipient
            $templateId
        );
        
        try {
            /** @var Dolist_Net_Model_Service $service */
            $service = Mage::getModel('dolist/service');
            // Send message using webservice
            $service->dolistEmtSendmail($message, $templateId, $storeId);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        return true;
    }
    
    /**
     * Generate Data field in SendMessageRequest Object used in SendMessage method
     * 
     * @param string $fromMailLeftPart From mail left part
     * @param string $fromName         From name
     * @param string $text             Email content (same used for plain text and html)
     * @param string $replyToMail      Reply to mail
     * @param string $replyToName      Reply to name => currently not implemented by Dolist-EMT webservice
     * @param string $subject          Email subject
     * @param array  $recipients       Array of recipients
     * @param string $templateId       Dolist-EMT template ID
     * 
     * @return string SendMessageRequest Data
     */
    protected function _generateSendMessageRequestData(
        $fromMailLeftPart, $fromName, $text, $replyToMail, $replyToName, $subject, $recipients, $templateId
    )
    {
        $message = "";
        
        // Generate message
        $domTree = new DOMDocument('1.0', 'UTF-8');
        $rootNode = $domTree->createElement('emtroot');
        $rootNode = $domTree->appendChild($rootNode);
        
        $messageNode = $domTree->createElement("MESSAGE");
        $messageNode = $rootNode->appendChild($messageNode);
        
        $messageNode->appendChild($domTree->createElement('FROMMAILLEFTPART', $this->_getFromMailLeftPart($fromMailLeftPart)));
        $messageNode->appendChild($domTree->createElement('FROMNAME', $fromName));
        
        // HTMLCONTENT
        $htmlContentNode = $domTree->createElement('HTMLCONTENT');
        $htmlContentCdata = $domTree->createCDATASection($text);
        $htmlContentNode->appendChild($htmlContentCdata);
        $messageNode->appendChild($htmlContentNode);
        
        $messageNode->appendChild($domTree->createElement('REPLYTOMAIL', $fromMailLeftPart)); // Full address
        $messageNode->appendChild($domTree->createElement('REPLYTONAME', $fromName));
        
        // SUBJECT
        $subjectContentNode = $domTree->createElement('SUBJECT');
        $subjectContentCdata = $domTree->createCDATASection($subject);
        $subjectContentNode->appendChild($subjectContentCdata);
        $messageNode->appendChild($subjectContentNode);
        
        $message = array(
            'Data'                  => $domTree->saveXML(),
            'MessageContentType'    => 'EmailMultipart', 
            'Recipient'             => $recipients[0], // Only one recipient is allowed
            'TemplateID'            => $templateId
        );
        
        return $message;
    }
    
    /**
     * Return email truncated after first occurence of '@' character
     * Permit to use left part to send email from <truncatedemail>@adm.dolist.net
     * 
     * @param string $email Email
     * 
     * @return string
     */
    protected function _getFromMailLeftPart($email)
    {
        $truncated = $email;
        $pos = strpos($email, '@');
        if ($pos != false) {
            $truncated = substr($email, 0, $pos);
        }
        return $truncated;
    }
    
    /**
     * Return Dolist-EMT template ID to use for given $magentoTemplateId
     * $magentoTemplateId can be string for default email templates or int for custom email templates (in BO)
     * 
     * @param string|int $magentoTemplateId Magento email template ID
     * 
     * @return int Dolist-EMT template id
     */
    protected function _getDolistEmtTemplateId($magentoTemplateId)
    {
        // To know scope of this send => use correct Dolist-EMT configuration scope
        $storeId = $this->getDesignConfig()->getStore();
        
        // Default template id
        $dolistEmtTemplateId = Mage::getStoreConfig(self::XML_DOLIST_EMT_DEFAULT_TEMPLATE, $storeId);
        
        // Build mapping array
        $templateMapping = array();
        
        foreach (unserialize(Mage::getStoreConfig(self::XML_DOLIST_EMT_TEMPLATE_MAPPING, $storeId)) as $mapping) {
            if (array_key_exists('dolist_template', $mapping)) {
                $templateMapping[$mapping['magento_template']] = $mapping['dolist_template'];
            }
        }
        
        // Return specific mapping if found
        if (array_key_exists($magentoTemplateId, $templateMapping)) {
            $dolistEmtTemplateId = $templateMapping[$magentoTemplateId];
        }
        
        return $dolistEmtTemplateId;
    }
}