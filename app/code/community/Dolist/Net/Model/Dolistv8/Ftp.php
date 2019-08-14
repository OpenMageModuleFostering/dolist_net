<?php
/**
 * Dolist FTP client
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Dolistv8_Ftp extends Varien_Io_Ftp
{
    const XML_DOLIST_V8_LOGIN          = 'dolist/dolist_v8/login';
    const XML_DOLIST_V8_PASSWORD       = 'dolist/dolist_v8/password';
    
    protected $_args = null;
    
    /**
     * Intialize arguments
     * 
     * @param array $params Init params
     * 
     * @return Dolist_Net_Model_Dolistv8_Ftp Ftp connection
     */
    public function __construct($params=null)
    {
        // Init params
        $storeId = 0;
        if (array_key_exists('store_id', $params)) {
            $storeId = $params['store_id'];
        }
        
        
        $this->_args = array(
            'host'      => (string) Mage::getConfig()->getNode('dolistparams/ftp/host'),
            'user'      => Mage::getStoreConfig(self::XML_DOLIST_V8_LOGIN, $storeId),
            'password'  => Mage::getStoreConfig(self::XML_DOLIST_V8_PASSWORD, $storeId),
            'passive'   => true // Enable passive mode, mandatory when target is behind firewall
        );
        
        Mage::log('FTP CONNECTION for store ' . $storeId);
        Mage::log($this->_args);
        return $this;
    }
    
    /**
     * Fix login and password
     * 
     * @param string $login    Optional login (used to test connection in back office)
     * @param string $password Optional password (used to test connection in back office)
     * 
     * @return Dolist_Net_Model_Dolistv8_Ftp Ftp connection
     */
    public function fixArgs($login=null, $password = null)
    {
        $this->_args = array(
            'host'      => (string) Mage::getConfig()->getNode('dolistparams/ftp/host'),
            'user'      => $login,
            'password'  => $password,
            'passive'   => true // Enable passive mode, mandatory when target is behind firewall
        );
    }
    
    /**
     * Init FTP connection to Dolist-V8 FTP server
     * 
     * @return boolean Connection OK or NOK
     */
    protected function _openConnection()
    {
        $connectionStatus = false;

        try {
            $connectionStatus = $this->open($this->_args);
        } catch (Varien_Io_Exception $e) {
            $message = 'Dolist-V8 FTP Connection: ' . $e->getMessage();
            // Store error message in session to display it in Back Office
            Mage::getSingleton('core/session')->setLastDolistFaultDetail($message);
            $this->_getHelper()->logError($message);
        }
        return $connectionStatus;
    }
    
    /**
     * Init FTP connection to Dolist-V8 FTP server
     * 
     * @return Dolist_Net_Model_Dolistv8_Ftp FTP connection
     */
    public function getConnection()
    {
        $this->_openConnection();
        return $this;
    }
    
    /**
     * Init FTP connection to Dolist-V8 FTP server
     * 
     * @return boolean Connection OK or NOK
     */
    public function getStatus()
    {
        $connectionStatus = $this->_openConnection();
        return $connectionStatus;
    }
    
    /**
     * Retrieve model helper
     *
     * @return Dolist_Net_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('dolist');
    }
}