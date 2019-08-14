<?php

/**
 * Calculated attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Date_FirstOrder extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * @param int $customerId
     * @param int $storeId
     * @return mixed
     */
    public function calculatedAttributeRequest($customerId, $storeId)
    {
        $readAdapter = $this->_getReadAdapter();

        $tableName = null;
        if (Mage::helper('dolist')->isFlatTableEnabled()) {
            $tableName = 'sales_flat_order';
        } else {
            $tableName = 'sales_order';
        }

        $select = $readAdapter->select()
            ->from(array('o' => (string)Mage::getConfig()->getTablePrefix() . $tableName),
                array(
                    'date' => 'MIN(created_at)'
                ))
            ->where('o.customer_id = ?', $customerId)
            ->where('o.store_id = ?', $storeId);

        $row = $readAdapter->fetchOne($select);
        return $row;
    }
}