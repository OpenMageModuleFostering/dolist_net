<?php

class Dolist_Net_Model_Exporter_Adapter_Int extends Dolist_Net_Model_Exporter_Adapter_Default
{
    public function getExportedValue($value)
    {
        $v = explode('.', $value);
        return $v[0];
    }
}