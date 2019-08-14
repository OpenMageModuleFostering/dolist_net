<?php
/**
 * Calculated attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_LastNotOrderedCartAmountInclTax extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * @param int $customerId
     * @param int $storeId
     * @return mixed
     */
    public function calculatedAttributeRequest($customerId, $storeId)
    {
        $readAdapter = $this->_getReadAdapter();
        $quoteTableName = null;
        if (Mage::helper('dolist')->isFlatTableEnabled()) {
            $quoteTableName = 'sales_flat_quote';
        } else {
            $quoteTableName = 'sales_flat_quote';
        }

        /** @var Varien_Db_Select $request */
        $request = $readAdapter->select()
            ->from(
                array('q' => Mage::helper('dolist')->getTablename($quoteTableName)),
                array(
                    'subtotal' => '(q.base_subtotal_with_discount*q.base_to_global_rate)',
                )
            )
            ->where('q.customer_id = ?', $customerId)
            ->where('q.is_active = 1')
            ->where('q.items_count > 0')
            ->where('q.store_id = ?', $storeId)
            ->order('q.updated_at DESC');

        $row = $readAdapter->fetchOne($request);

        return $row === false ? null : $row;
    }
}