<?php
/**
 * Calculated attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgNbDifferentProductsPerOrderLine extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * Perform SQl request for calculated attribute and save result in cache
     * Call to this method is performed before exporting, to be done only once.
     *
     * @param int $customerId
     * @param int $storeId
     * @param array $dates
     * @return mixed
     */
    public function calculatedAttributeRequest($customerId, $storeId, $dates=array())
    {
        $readAdapter = $this->_getReadAdapter();

        $select = null;
        if (Mage::helper('dolist')->isFlatTableEnabled()) {
            $orderTableName = 'sales_flat_order';
            
            // Nested request
            $select = $readAdapter->select()
                ->from(
                    array('o' => (string)Mage::getConfig()->getTablePrefix() . $orderTableName),
                    array(
                        'AVG(total_qty_ordered / total_item_count) AS avg_products_per_line'
                    )
                )
                ->where('o.customer_id = ?', $customerId)
                ->where('o.store_id = ?', $storeId);
        } else {
            $orderTableName = 'sales_order';
            $orderItemTableName = 'sales_flat_order_item';

            $select = $readAdapter->select()
            ->from(
                    array('o' => (string)Mage::getConfig()->getTablePrefix() . $orderTableName),
                    array(
                            'SUM(o.total_qty_ordered)/count(sf.item_id) as avg_products_per_line'
                    )
            )->joinLeft(
                    array('sf' => Mage::helper('dolist')->getTablename($orderItemTableName)),
                    "sf.order_id = o.entity_id",
                    array()
            )
            ->where('o.customer_id = ?', $customerId)
            ->where('o.store_id = ?', $storeId);
        }

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