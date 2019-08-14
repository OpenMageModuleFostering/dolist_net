<?php

class Dolist_Net_Model_Mysql4_Reports extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('dolist/reports', 'id');
    }
} 