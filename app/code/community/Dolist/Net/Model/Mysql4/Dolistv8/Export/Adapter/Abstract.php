<?php
/**
 * Abstract attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * Retrieve connection for read data
     * 
     * @return mixed
     */
    protected function _getReadAdapter()
    {
        return Mage::getModel('core/resource')->getConnection('read');
    }

    /**
     * Return table name including prefix
     * 
     * @param string $tableName Table name
     * 
     * @return string Table name
     */
    public function getTable($tableName)
    {
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        return $tablePrefix . $tableName;
    }
    
    /**
     * Transform date to Dolist format
     * 
     * @param string $inputDate Input date
     * 
     * @return string Dolist formatted date
     */
    public function formatDate($inputDate)
    {
        $outputDate = new Zend_Date($inputDate, 'Y-m-d H:i:s');
        return $outputDate->toString('dd/MM/YYYY');
    }
    
    /**
     * Round string to integer, Dolist format
     * 
     * @param string $inputInt Input string
     * 
     * @return int Dolist formatted rounded integer
     */
    public function formatInt($inputInt)
    {
        return round($inputInt);
    }
    
    /**
     * Retrieve default helper
     * 
     * @return Dolist_Net_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('dolist');
    }
}