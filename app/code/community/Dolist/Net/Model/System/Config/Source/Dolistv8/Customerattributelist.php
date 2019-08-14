<?php
/**
 * Dolist source chooser to select customer attribute list which can be exported to Doslit-V8
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_System_Config_Source_Dolistv8_Customerattributelist
{
    protected $_staticAttrByBackend = array(
        'datetime' => array('created_at', 'updated_at'),
        'int' => array('entity_id', 'entity_type_id', 'attribute_set_id', 'increment_id')
    );

    protected $_staticAttrToExcludeByBackend = array(
        'static' => array('created_at', 'updated_at'),
        'varchar' => array('entity_id', 'entity_type_id', 'attribute_set_id', 'increment_id')
    );
    
    /**
     * Return Dolist-V8 customer attribute list
     * Contain automatic Magento attributes, and also custom attributes dynamically generated (specific sql requests)
     * defined in config.xml file
     * 
     * @param array|string $backendType Backend type to display. Can be static|int|varchar|datetime
     *
     * @return array Options with labels and values. Values are models for calculated attributes
     */
    public function toOptionArray($backendType)
    {
        if (!is_array($backendType)) {
            $backendType = array($backendType);
        }
        //prepare static attr to add
        $staticAttrToAdd = array();
        foreach ($backendType as $type) {
            if(isset($this->_staticAttrByBackend[$type])) {
                $staticAttrToAdd = array_merge($staticAttrToAdd, $this->_staticAttrByBackend[$type]);
            }
        }

        $staticAttrToExclude = array();
        foreach ($backendType as $type) {
            if(isset($this->_staticAttrToExcludeByBackend[$type])) {
                $staticAttrToExclude = array_merge($staticAttrToExclude, $this->_staticAttrToExcludeByBackend[$type]);
            }
        }
        
        $customerModel = Mage::getModel('customer/customer');
        $attributes = $customerModel->getAttributes();
        
        // Customer attributes
        $options = array();
        if (!empty($attributes)) {
            $options[] = array('value' => array(), 'label' => Mage::helper('dolist')->__("Customer attributes"));
        }
        foreach ($attributes as $att) {
            /** @var Mage_Customer_Model_Attribute $att  */
            if(in_array($att->getAttributeCode(), array('default_shipping', 'default_billing', 'email', 'is_confirmed'))) {
                continue;
            }

            if(in_array($att->getAttributeCode(), Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {
                continue;
            }

            // Filter following backend type
            if ((in_array($att->getBackendType(), $backendType) && !in_array($att->getAttributeCode(), $staticAttrToExclude)) || in_array($att->getAttributeCode(), $staticAttrToAdd)) {
                $options[] = array('value' => $att->getAttributeCode(), 'label' => Mage::helper('customer')->__($att->getFrontendLabel()));
            }
            
        }

        // Calculated attributes
        $calculatedAttributes = Mage::getConfig()->getNode('dolistparams/contact_export_row_adapter/calculated_attributes')->asArray();
        foreach ($calculatedAttributes as $key => $calculatedAttributeList) {
            // Display these attributes
            if (in_array($key, $backendType)) {
                $options[] = array('value' => array(), 'label' => Mage::helper('dolist')->__("Calculated attributes"));

                if(is_array($calculatedAttributeList)) {
                    foreach ($calculatedAttributeList as $id => $calculatedAttributeItem) {

                        if ($calculatedAttributeItem['adapter'] == "separator") {
                            $options[] = array(
                                'value' => array(),
                                'label' => Mage::helper('dolist')->__($calculatedAttributeItem['label'])
                            );
                        } else {
                            $options[] = array(
                                'value' => $id,
                                'label' => Mage::helper('dolist')->__($calculatedAttributeItem['label'])
                            );
                        }
                    }
                }
            }
        }

        return $options;
    }
}
