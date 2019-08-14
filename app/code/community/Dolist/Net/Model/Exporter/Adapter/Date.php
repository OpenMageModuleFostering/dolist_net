<?php

class Dolist_Net_Model_Exporter_Adapter_Date extends Dolist_Net_Model_Exporter_Adapter_Default
{
    public function getExportedValue($value)
    {
        if ($value != '') {
            //$date = new Zend_Date($value, 'Y-m-d H:i:s');
            $date = new Zend_Date($value, Zend_Date::ISO_8601);
            $return = $date->toString('dd/MM/yyyy');
            return $return;
        }
        return null;
    }
} 