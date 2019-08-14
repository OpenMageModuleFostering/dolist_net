<?php

/**
 * Class Dolist_Net_Model_Dolistv8_Calculatedfields
 *
 * @method $this setStoreId(int $storeId)
 */
class Dolist_Net_Model_Dolistv8_Calculatedfields extends Mage_Core_Model_Abstract
{

    protected $debug;

    const FULL = 1;
    const BEGIN_DATE = 2;
    const RANGE_1 = 3;
    const RANGE_3 = 4;
    const RANGE_6 = 5;
    const RANGE_12 = 6;
    const RANGE_24 = 7;

    private static $watchedAttributes = array(
        'id',
        'customer_id',
        'first_order_amount',
        'first_order_amount_with_vat',
        'last_order_amount',
        'last_order_amount_with_vat',
        'total_orders_amount',
        'total_orders_amount_with_vat',
        'average_unique_product_count',
        'average_product_count_by_command_line',
        'total_product_count',
        'total_orders_count',
        // 'last_unordered_cart_amount', (change to this fields should not change the updated_at date. This fields is always recomputed in export)
        // 'last_unordered_cart_amount_with_vat', (change to this fields should not change the updated_at date. This fields is always recomputed in export)
        'discount_rule_count',
        'last_orders_range',
        'first_order_date',
        'last_order_date',
        // 'last_unordered_cart_date', (change to this fields should not change the updated_at date. This fields is always recomputed in export)
        'orders_expire',
        'cart_expire',
        'config',
        'start_date',
        'store_id',
    );

    protected function _construct()
    {
        parent::_construct();
        $this->_init('dolist/dolistv8_calculatedfields');
        $this->debug = true;
    }

    public function getConfig()
    {
        return array(
            'first_order_amount' => 'first_order_amount',
            'first_order_amount_with_vat' => 'first_order_amount_with_vat',
            'last_order_amount' => 'last_order_amount',
            'last_order_amount_with_vat' => 'last_order_amount_with_vat',
            'total_orders_amount' => 'total_orders_amount',
            'total_orders_amount_with_vat' => 'total_orders_amount_with_vat',
            'average_unique_product_count' => 'average_unique_product_count',
            'average_product_count_by_command_line' => 'average_product_count_by_command_line',
            'total_product_count' => 'total_product_count',
            'total_orders_count' => 'total_orders_count',
            'last_unordered_cart_amount' => 'last_unordered_cart_amount',
            'last_unordered_cart_amount_with_vat' => 'last_unordered_cart_amount_with_vat',
            'discount_rule_count' => 'discount_rule_count',
            'last_orders_range' => 'last_orders_range',
            'first_order_date' => 'first_order_date',
            'last_order_date' => 'last_order_date',
            'last_unordered_cart_date' => 'last_unordered_cart_date',
            'avg_order_amount_excl_tax' => 'avg_order_amount_excl_tax',
            'avg_order_amount_incl_tax' => 'avg_order_amount_incl_tax',
            'avg_nb_products_per_order' => 'avg_nb_products_per_order',

        );
    }

    public function isCalculatedFieldConfig($key)
    {
        $config = $this->getConfig();

        if (array_key_exists($key, $config)) {
            return true;
        }
        return false;
    }

    /**
     * @return Dolist_Net_Model_Mysql4_Dolistv8_Calculatedfields
     */
    public function getResource()
    {
        return parent::getResource();
    }

    /**
     * @param $customerId
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        $this->getResource()->loadByCustomerId($this, $customerId);

        return $this;
    }

    /**
     * @param array $attribute
     * @return mixed
     */
    public function getColumnValue($attribute)
    {
        $config = $this->getConfig();
        $adapter = Mage::getModel($attribute['adapter']);

        return $adapter->getExportedValue($this->getData($config[$attribute['field']]));
    }

    /**
     * @return array
     */
    public function computeOrderDataTtl()
    {
        /**
         * FULL : ttl date is null
         * BEGIN : ttl is from beginDate to currentDate
         * RANGE_X : ttl is from currentDate - delay to currentDate
         */
        $option = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_mode', $this->getData('store_id'));

        if (!$option) {
            $option = 1;
        }

        $start = null;
        $stop = null;

        switch ($option) {
            case self::FULL:
                break;
            case self::BEGIN_DATE:
                $date = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_date', $this->getData('store_id'));
                $timestamp = strtotime($date);
                $start = new \DateTime();
                $start->setTimestamp($timestamp);
                break;
            case self::RANGE_1:
                $delay = 1;
                $startDate = new \DateTime('-' . $delay . ' months');
                $strDate = $this->getResource()->getFirstOrderDate($this->getData('customer_id'), $startDate);
                if ($strDate) {
                    $startDate->setTimestamp(strtotime($strDate));
                    $start = $startDate;
                    $stop = new \DateTime();
                    $stop->setTimestamp(strtotime($strDate));
                    $stop->modify('+' . $delay . ' months');
                }
                break;
            case self::RANGE_3:
                $delay = 3;
                $startDate = new \DateTime('-' . $delay . ' months');
                $strDate = $this->getResource()->getFirstOrderDate($this->getData('customer_id'), $startDate);
                if ($strDate) {
                    $startDate->setTimestamp(strtotime($strDate));
                    $start = $startDate;
                    $stop = new \DateTime();
                    $stop->setTimestamp(strtotime($strDate));
                    $stop->modify('+' . $delay . ' months');
                }
                break;
            case self::RANGE_6:
                $delay = 6;
                $startDate = new \DateTime('-' . $delay . ' months');
                $strDate = $this->getResource()->getFirstOrderDate($this->getData('customer_id'), $startDate);
                if ($strDate) {
                    $startDate->setTimestamp(strtotime($strDate));
                    $start = $startDate;
                    $stop = new \DateTime();
                    $stop->setTimestamp(strtotime($strDate));
                    $stop->modify('+' . $delay . ' months');
                }
                break;
            case self::RANGE_12:
                $delay = 12;
                $startDate = new \DateTime('-' . $delay . ' months');
                $strDate = $this->getResource()->getFirstOrderDate($this->getData('customer_id'), $startDate);
                if ($strDate) {
                    $startDate->setTimestamp(strtotime($strDate));
                    $start = $startDate;
                    $stop = new \DateTime();
                    $stop->setTimestamp(strtotime($strDate));
                    $stop->modify('+' . $delay . ' months');
                }
                break;
            case self::RANGE_24:
                $delay = 24;
                $startDate = new \DateTime('-' . $delay . ' months');
                $strDate = $this->getResource()->getFirstOrderDate($this->getData('customer_id'), $startDate);
                if ($strDate) {
                    $startDate->setTimestamp(strtotime($strDate));
                    $start = $startDate;
                    $stop = new \DateTime();
                    $stop->setTimestamp(strtotime($strDate));
                    $stop->modify('+' . $delay . ' months');
                }
                break;
        }

        return array(
            'start' => $start,
            'stop' => $stop
        );
    }

    /**
     * @return array
     */
    public function computeCartDataTtl()
    {
        /**
         * FULL : ttl date is null
         * BEGIN : ttl is from beginDate to currentDate
         * RANGE_X : ttl is from currentDate - delay to currentDate
         */
        $option = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_mode', $this->getData('store_id'));

        if (!$option) {
            $option = 1;
        }

        $start = null;
        $stop = null;

        switch ($option) {
            case self::FULL:
                break;
            case self::BEGIN_DATE:
                $date = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_date', $this->getData('store_id'));
                $timestamp = strtotime($date);
                $start = new \DateTime();
                $start->setTimestamp($timestamp);
                break;
            case self::RANGE_1:
                $start = new \DateTime('-1 months');
                break;
            case self::RANGE_3:
                $start = new \DateTime('-3 months');
                break;
            case self::RANGE_6:
                $start = new \DateTime('-6 months');
                break;
            case self::RANGE_12:
                $start = new \DateTime('-12 months');
                break;
            case self::RANGE_24:
                $start = new \DateTime('-24 months');
                break;
        }

        return array(
            'start' => $start,
            'stop' => $stop
        );
    }

    /**
     * @return $this
     */
    public function compute()
    {
        if (!$this->hasData('customer_id')) {
            Mage::logException(new Exception('Missing customer ID to compute'));

            return $this;
        }

        if (!$this->hasData('store_id')) {
            Mage::logException(new Exception('Missing store ID to compute'));

            return $this;
        }

        $datesOrder = $this->computeOrderDataTtl();
        $datesCart = $this->computeCartDataTtl();
        $config = Mage::getStoreConfig("dolist/dolist_v8/calculatedfieds_mode", $this->getData('store_id'));

        $this->addData(array(
            'first_order_amount' => $this->computeFirstOrderAmount(),
            'first_order_amount_with_vat' => $this->computeFirstOrderAmount(true),
            'last_order_amount' => $this->computeLastOrderAmount(),
            'last_order_amount_with_vat' => $this->computeLastOrderAmount(true),
            'total_orders_amount' => $this->computeTotalOrdersAmount($datesOrder),
            'total_orders_amount_with_vat' => $this->computeTotalOrdersAmount($datesOrder, true),
            'average_unique_product_count' => $this->computeAverageUniqueProductCount($datesOrder),
            'average_product_count_by_command_line' => $this->computeAverageProductCountByCommandLine($datesOrder),
            'total_product_count' => $this->computeTotalProductCount($datesOrder),
            'total_orders_count' => $this->computeTotalOrdersCount($datesOrder),
            'last_unordered_cart_amount' => $this->computeLastUnorderedCartAmount($datesCart),
            'last_unordered_cart_amount_with_vat' => $this->computeLastUnorderedCartAmount($datesCart, true),
            'discount_rule_count' => $this->computeDiscountRuleCount(),
            'last_orders_range' => $this->getLastTwoOrdersRange(),
            'first_order_date' => $this->getFirstOrderDate(),
            'last_order_date' => $this->getLastOrderDate(),
            'last_unordered_cart_date' => $this->getLastUnorderedCartDate(),
            'config' => $config,
            'start_date' => ($config == $this::BEGIN_DATE) ? Mage::getStoreConfig("dolist/dolist_v8/calculatedfieds_date", $this->getData('store_id')) : null,
        ));

        if (is_array($datesOrder) && !empty($datesOrder) && array_key_exists('stop', $datesOrder) && $datesOrder['stop']) {
            $this->setData('orders_expire', $datesOrder['stop']->format('Y-m-d H:i:s'));
        }

        if (is_array($datesCart) && !empty($datesCart) && array_key_exists('stop', $datesCart) && $datesCart['stop']) {
            $this->setData('cart_expire', $datesCart['stop']->format('Y-m-d H:i:s'));
        }

        try {
            $this->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    protected function _beforeSave()
    {
        $hasWatchedAttributeChange = false;

        foreach (self::$watchedAttributes as $attribute) {
            if ($this->dataHasChangedFor($attribute)) {
                $hasWatchedAttributeChange = true;

                break;
            }
        }

        if ($hasWatchedAttributeChange) {
            $now = new \DateTime('now');
            $this->setData('updated_at', $now->format('Y-m-d H:i:s'));
        }

        return parent::_beforeSave();
    }


    /**
     * @param bool $withVat
     *
     * @return float
     */
    public function computeFirstOrderAmount($withVat = false)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_FirstOrderAmountExclTax|Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_FirstOrderAmountInclTax $model */
        $model = null;

        if ($withVat)
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_firstorderamountincltax');
        else {
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_firstorderamountexcltax');
        }

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'));

        if (!$value) {
            return null;
        }

        return round(floatval($value), 2);
    }

    /**
     * @param bool $withVat
     *
     * @return float
     */
    public function computeLastOrderAmount($withVat = false)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_LastOrderAmountExclTax|Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_LastOrderAmountInclTax $model */
        $model = null;

        if ($withVat)
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_lastorderamountincltax');
        else {
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_lastorderamountexcltax');
        }

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'));

        if (!$value) {
            return null;
        }

        return round(floatval($value), 2);
    }

    /**
     * @param array $dates
     * @param bool $withVat
     *
     * @return float
     */
    public function computeTotalOrdersAmount($dates = array(), $withVat = false)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_TotalOrderAmountExclTax|Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_TotalOrderAmountInclTax $model */
        $model = null;
        if ($withVat)
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_totalorderamountincltax');
        else {
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_totalorderamountexcltax');
        }

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return null;
        }

        return round(floatval($value), 2);
    }


    /**
     * @param array $dates
     * @param bool $withVat
     *
     * @return float
     */
    public function computeAverageOrdersAmount($dates = array(), $withVat = false)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgOrderAmountExclTax|Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgOrderAmountInclTax $model */
        $model = null;
        if ($withVat)
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_avgorderamountincltax');
        else {
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_avgorderamountexcltax');
        }

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return null;
        }

        return round(floatval($value), 2);
    }

    public function getAvgOrdersAmount($withVat = false)
    {
        $count = $this->getData('total_orders_count');
        $amount = $this->getData(!$withVat ? 'total_orders_amount' : 'total_orders_amount_with_vat');

        if ($count != 0) {
            return round(floatval($amount / $count), 2);
        } else {
            return null;
        }


    }

    /**
     * @param array $dates
     *
     * @return float
     */
    public function computeAverageProductCount($dates = array())
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgNbProductsPerOrder $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_avgnbproductsperorder');

        return $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);
    }

    public function getAvgProductCount()
    {
        $orderCount = $this->getData('total_orders_count');
        $productCount = $this->getData('total_product_count');

        if($orderCount != 0) {
            return round(floatval($productCount / $orderCount), 2);
        }
        else {
            return 0;
        }
    }

    /**
     *
     * @param array $dates
     * @return float
     */
    public function computeAverageUniqueProductCount($dates = array())
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgNbDifferentProductsPerOrder $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_avgnbdifferentproductsperorder');

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return 0;
        }

        return round(floatval($value), 1);
    }

    /**
     * @param $customerId
     *
     * @param array $dates
     * @return float
     */
    public function computeAverageProductCountByCommandLine($dates = array())
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_AvgNbDifferentProductsPerOrderLine $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_avgnbdifferentproductsperorderline');

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return 0;
        }

        return round(floatval($value), 1);
    }

    /**
     *
     * @param array $dates
     * @return float
     */
    public function computeTotalProductCount($dates = array())
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_TotalOrderedProducts $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_totalorderedproducts');

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return 0;
        }

        return intval($value);
    }

    /**
     *
     * @param array $dates
     * @return float
     */
    public function computeTotalOrdersCount($dates = array())
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_TotalOrders $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_totalorders');

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return 0;
        }

        return intval($value);
    }

    /**
     *
     * @param array $dates
     * @param bool $withVat
     *
     * @return float
     */
    public function computeLastUnorderedCartAmount($dates = array(), $withVat = false)
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_LastNotOrderedCartAmountExclTax|Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_LastNotOrderedCartAmountInclTax $model */
        $model = null;
        if ($withVat)
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_lastnotorderedcartamountincltax');
        else {
            $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_lastnotorderedcartamountexcltax');
        }

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'), $dates);

        if (!$value) {
            return null;
        }

        return round(floatval($value), 2);
    }

    /**
     *
     * @param array $dates
     * @return float
     */
    public function computeDiscountRuleCount()
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Int_NbPromotionRulesUsed $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_int_nbpromotionrulesused');

        $value = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'));

        if (!$value) {
            return 0;
        }

        return intval($value);
    }

    /**
     *
     * @return DateTime
     */
    public function getFirstOrderDate()
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Date_FirstOrder $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_date_firstorder');

        $date = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'));

        $value = date_create_from_format('Y-m-d H:i:s', $date);

        if (!$value) {
            return null;
        }

        return $value->format('Y-m-d');
    }

    /**
     *
     * @return DateTime
     */
    public function getLastOrderDate()
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Date_LastOrder $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_date_lastorder');

        $date = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'));

        $value = date_create_from_format('Y-m-d H:i:s', $date);

        if (!$value) {
            return null;
        }

        return $value->format('Y-m-d');
    }

    public function getLastTwoOrdersRange()
    {
        $readAdapter = $this->_getReadAdapter();

        $tableName = null;
        if($this->getDolistMainHelper()->isFlatTableEnabled()){
            $tableName = 'sales_flat_order';
        }else{
            $tableName = 'sales_order';
        }

        /** @var Varien_Db_Select $select */
        $select = $readAdapter->select();
        $select->from(array('o' => $this->getTable($tableName)), array('o.created_at'))
            ->where('o.customer_id=?', $this->getData('customer_id'))
            ->where('o.store_id=?', $this->getData('store_id'))
            ->order('o.created_at DESC')
            ->limit(2)
        ;

        $data = $readAdapter->fetchAll($select);

        if(count($data) < 2) {
            return 0;
        }

        $diff = date_create_from_format('Y-m-d H:i:s', $data[1]['created_at'])->diff(date_create_from_format('Y-m-d H:i:s', $data[0]['created_at']));

        return $diff->days;
    }

    /**
     *
     * @return DateTime
     */
    public function getLastUnorderedCartDate()
    {
        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Export_Adapter_Date_LastQuoteNotOrdered $model */
        $model = Mage::getModel('dolist_mysql4/dolistv8_export_adapter_date_lastquotenotordered');

        $date = $model->calculatedAttributeRequest($this->getData('customer_id'), $this->getData('store_id'));

        $value = date_create_from_format('Y-m-d H:i:s', $date);

        if (!$value) {
            return null;
        }

        return $value->format('Y-m-d');
    }

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
     * @return Dolist_Net_Helper_Data
     */
    public function getDolistMainHelper()
    {
        return Mage::helper('dolist');
    }
}