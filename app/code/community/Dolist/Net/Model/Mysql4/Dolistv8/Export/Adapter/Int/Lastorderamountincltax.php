<?php
/**
 * Calculated attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_LastOrderAmountInclTax extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * Perform SQl request for calculated attribute and save result in cache
     * Call to this method is performed before exporting, to be done only once.
     *
     * @param int $customerId
     * @param int $storeId
     * @return mixed
     */
    public function calculatedAttributeRequest($customerId, $storeId)
    {
        $readAdapter    = $this->_getReadAdapter();
        $orderTableName = null;
        if (Mage::helper('dolist')->isFlatTableEnabled()) {
            $orderTableName = 'sales_flat_order';
        } else {
            $orderTableName = 'sales_order';
        }
        $select = $readAdapter
            ->select()
            ->from(
                array('o' => $this->getTable($orderTableName)),
                array(
                    'total' => 'o.grand_total'
                )
            )
            ->where('o.customer_id = ?', $customerId)
            ->where('o.store_id = ?', $storeId)
            ->order('o.created_at DESC');

        
        $row = $readAdapter->fetchOne($select);
        return $row;
    }
}