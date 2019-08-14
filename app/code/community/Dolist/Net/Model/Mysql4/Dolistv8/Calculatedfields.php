<?php

class Dolist_Net_Model_Mysql4_Dolistv8_Calculatedfields extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_isPkAutoIncrement=false;
        $this->_init('dolist/dolistv8_calculatedfields', 'id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        parent::load($object, $value, $field);

        $object->setDataChanges(false);
        $object->setOrigData();

        return $this;
    }


    /**
     * @param $customerId
     * @param $startDate
     * @return mixed
     */
    public function getFirstOrderDate($customerId, $startDate){
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
                array('o' => $this->getTable($orderTableName)),
                array(
                    'date' => 'o.created_at'
                )
            )
            ->where('o.customer_id = ?', $customerId)
            ->where('o.created_at >= ?', $startDate->format('Y-m-d h:g:s'))
            ->order('o.created_at ASC');

        $row = $readAdapter->fetchOne($select);
        return $row;
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     * @throws Exception
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIdField  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), 'store_id'));
            $select->where($storeIdField . '=?', (int)$object->getStoreId());
        }
        else {
            throw new \Exception('Unable to load calculated field without store_id');
        }

        return $select;
    }

    /**
     * Load calculated field by customer id
     * @param Dolist_Net_Model_Dolistv8_Calculatedfields $object
     * @param $customerId
     * @param bool $testOnly
     * @return $this
     * @throws Exception
     */
    public function loadByCustomerId(Dolist_Net_Model_Dolistv8_Calculatedfields $object, $customerId, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $this->_getLoadSelect('customer_id', $customerId, $object);

        $objectId = $adapter->fetchOne($select);
        if ($objectId) {
            $this->load($object, $objectId);
        } else {
            $object->setData(array(
                'store_id' => $object->getData('store_id'),
                'customer_id' => $customerId,
            ));
        }

        return $this;
    }
}