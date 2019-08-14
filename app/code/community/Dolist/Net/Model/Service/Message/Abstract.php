<?php
/**
 * Dolist SOAP abstract message
 * Convert stdClass Object to Varien Object
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Service_Message_Abstract extends Varien_Object
{
    const XML_DOLIST_EMT_ACCOUNTID              = 'dolist/dolist_emt/accountid';
    const XML_DOLIST_EMT_AUTHENTICATION_KEY     = 'dolist/dolist_emt/authentication_key';
    
    const XML_DOLIST_V8_ACCOUNTID               = 'dolist/dolist_v8/accountid';
    const XML_DOLIST_V8_AUTHENTICATION_KEY      = 'dolist/dolist_v8/authentication_key';
    
    /**
     * Use web service convention for attribute names
     * Converts field names for setters and getters
     * $this->setMyField($value) === $this->setData('myField', $value)
     * 
     * Do not use cache to avoid side effects with Varien_Object one
     *
     * @param string $name Attribute name
     * 
     * @return string
     */
    protected function _underscore($name)
    {
        // lcfist is only available since 5.3, the line below is equivalent to "$name = lcfirst($name);"
        $name[0] = strtolower($name[0]);
        return $name;
    }
}