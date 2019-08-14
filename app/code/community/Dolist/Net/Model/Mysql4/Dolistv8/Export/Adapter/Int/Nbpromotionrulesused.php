<?php
/**
 * Calculated attribute adapter
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_NbPromotionRulesUsed extends Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Abstract
{
    /**
     * Perform SQl request for calculated attribute and save result in cache
     * Call to this method is performed before exporting, to be done only once.
     *
     * @param int $customerId
     * @param int $storeId
     * @return
     */
    public function calculatedAttributeRequest($customerId, $storeId)
    {
        $readAdapter = $this->_getReadAdapter();
        
        $salesruleTableName = 'salesrule_customer';
        
        // Nested request
        $select = $readAdapter->select()
            ->from(
                array('s' => Mage::helper('dolist')->getTablename($salesruleTableName)),
                array(
                    'nb_rules'=>'SUM(s.times_used)'
                )
            )
            ->where('s.customer_id = ?', $customerId);

                
        $row = $readAdapter->fetchOne($select);

        return $row !== null ? $row : 0;
    }
}