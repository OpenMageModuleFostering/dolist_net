<?php

class Dolist_Net_Model_Adminhtml_System_Config_Source_Timeperiods
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::FULL,
                'label' => Mage::helper('dolist')->__('All datas'),
            ),
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::BEGIN_DATE,
                'label' => Mage::helper('dolist')->__('From a specified start date'),
            ),
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_1,
                'label' => Mage::helper('dolist')->__('Data since 1 month'),
            ),
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_3,
                'label' => Mage::helper('dolist')->__('Data since 3 months'),
            ),
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_6,
                'label' => Mage::helper('dolist')->__('Data since 6 months'),
            ),
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_12,
                'label' => Mage::helper('dolist')->__('Data since 12 months'),
            ),
            array(
                'value' => \Dolist_Net_Model_Dolistv8_Calculatedfields::RANGE_24,
                'label' => Mage::helper('dolist')->__('Data since 24 months'),
            ),

        );
    }
} 