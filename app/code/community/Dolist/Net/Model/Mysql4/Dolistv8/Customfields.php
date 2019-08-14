<?php

class Dolist_Net_Model_Mysql4_Dolistv8_Customfields extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_isPkAutoIncrement=false;
        $this->_init('dolist/dolistv8_customfields', 'id');
    }
} 