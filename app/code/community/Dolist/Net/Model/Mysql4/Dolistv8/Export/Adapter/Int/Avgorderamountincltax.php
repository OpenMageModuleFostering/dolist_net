<?php
/**
 * Calculated attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgOrderAmountInclTax extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * @param int $customerId
     * @param int $storeId
     * @param array $dates
     * @return mixed
     */
    public function calculatedAttributeRequest($customerId, $storeId, $dates=array())
    {
        $readAdapter = $this->_getReadAdapter();
        $orderTableName = null;
        if (Mage::helper('dolist')->isFlatTableEnabled()) {
            $orderTableName = 'sales_flat_order';

        } else {
            $orderTableName = 'sales_order';
        }
        $select = $readAdapter
            ->select()
            ->from(
                array('o' => (string)Mage::getConfig()->getTablePrefix() . $orderTableName),
                array(
                    'total' => 'AVG(o.grand_total)'
                )
            )
            ->where('o.customer_id = ?', $customerId)
            ->where('o.store_id = ?', $storeId)
            ->order('o.created_at ASC');

        if (!empty($dates)) {
            if (array_key_exists('start', $dates) && $dates["start"] != null && $dates["start"] instanceof \DateTime) {
                $select->where('o.created_at > ?', $dates['start']->format('Y-m-d H:i:s'));
            }
            if (array_key_exists('stop', $dates) && $dates["stop"] != null && $dates["stop"] instanceof \DateTime) {
                $select->where('o.created_at < ?', $dates['stop']->format('Y-m-d H:i:s'));
            }
        }

        $row = $readAdapter->fetchOne($select);
        return $row;
    }
}