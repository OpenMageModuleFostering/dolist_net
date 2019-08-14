<?php

class Dolist_Net_Block_Adminhtml_Edit_Tab_Configuration extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_rows;

    protected $_columnsStr;

    protected $_columnsInt;

    protected $_columnsDate;

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $form->setHtmlIdPrefix('dolist_conf_');
        $options = array();

        $storeId = $this->getRequest()->getParam('store', 0);

        $fieldset = $form->addFieldset('data_dolist_form_config_v8', array(
            'legend' => Mage::helper('dolist')->__('Configuration')
        ));

        $fieldset->addField('store_id', 'hidden', array(
            'name'      => 'store_id',
            'value'     => $storeId
        ));

        $fieldset->addField('export_customer_with_order', 'select', array_merge($options, array(
            'name' => 'export_customer_with_order',
            'label' => Mage::helper('dolist')->__('Export non subscribed customer with existing order'),
            'class' => '',
            'required' => false,
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        )));

        $fieldset->addField('calculatedfieds_mode', 'select', array_merge($options, array(
            'name' => 'calculatedfieds_mode',
            'label' => Mage::helper('dolist')->__('Time period for calculated fields'),
            'class' => '',
            'required' => false,
            'values' => Mage::getModel('dolist/adminhtml_system_config_source_timeperiods')->toOptionArray(),
        )));

        $fieldset->addField('calculatedfieds_date', 'date', array_merge($options, array(
            'name' => 'calculatedfieds_date',
            'label' => Mage::helper('dolist')->__('Start date for calculated fields'),
            'class' => '',
            'required' => false,
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'image' => $this->getSkinUrl('images/grid-cal.gif')
        )));

        $fieldset = $form->addFieldset('data_dolist_form_config_custom_fields', array(
            'legend' => Mage::helper('dolist')->__('Custom Fields')
        ));

        $btnSync = $fieldset->addField('btnSyncCustomFields', 'button', array(
            'name' => 'btnSyncCustomFields',
            'value' => Mage::helper('dolist')->__('Synchronize Custom Fields'),
            //'style' => 'color:white;height:50px',  //just an example
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/updateCustomFields', array('store' => $storeId)) . '\')',
            'type' => 'button',
        ));
        $btnSync->setAfterElementHtml('
    <script type="text/javascript">
  document.getElementById("dolist_conf_btnSyncCustomFields").value = \'' . Mage::helper('dolist')->__('Synchronize Custom Fields') . '\';
 </script>');

        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $customFieldsCollection */
        $customFieldsCollection = Mage::getModel('dolist/dolistv8_customfields')
                ->getCollection()
                ->addFieldToFilter('scope_id', $storeId)
        ;

        foreach ($customFieldsCollection as $customField) {

            if ($customField->name == 'email') {
                continue;
            }

            if (array_key_exists($customField->name, Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {

                $fieldset->addField(sprintf('cstfield_%s', Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName[$customField->name]), 'select', array_merge($options, array(
                    'name' => sprintf('cstfield_%s', Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName[$customField->name]),
                    'label' => $customField->title,
                    'class' => '',
                    'required' => false,
                    'values' => Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray(),
                )));
            }

        }

        $this->_columnsStr = array();

        $this->_columnsStr['magento_customer_attribute'] = array(
            'label' => Mage::helper('dolist')->__('Magento customer attribute'),
            'renderer' => $this->_getMagentoCustomerAttributeStrRenderer()
        );

        $this->_columnsStr['dolist_custom_fields'] = array(
            'label' => Mage::helper('dolist')->__('Dolist-V8 attribute name'),
            'renderer' => $this->_getDolistv8AttributeStrRenderer()
        );

        $this->_columnsInt = array();

        $this->_columnsInt['magento_customer_attribute'] = array(
            'label' => Mage::helper('dolist')->__('Magento customer attribute'),
            'renderer' => $this->_getMagentoCustomerAttributeIntRenderer()
        );

        $this->_columnsInt['dolist_custom_fields'] = array(
            'label' => Mage::helper('dolist')->__('Dolist-V8 attribute name'),
            'renderer' => $this->_getDolistv8AttributeIntRenderer()
        );

        $this->_columnsDate = array();

        $this->_columnsDate['magento_customer_attribute'] = array(
            'label' => Mage::helper('dolist')->__('Magento customer attribute'),
            'renderer' => $this->_getMagentoCustomerAttributeDateRenderer()
        );

        $this->_columnsDate['dolist_custom_fields'] = array(
            'label' => Mage::helper('dolist')->__('Dolist-V8 attribute name'),
            'renderer' => $this->_getDolistv8AttributeDateRenderer()
        );

        $values = array();

        foreach ($customFieldsCollection as $customField) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */

            if (!$customField->getData('magento_field')) {
                continue;
            }

            if (array_key_exists($customField->getData('name'), Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {
                $values[sprintf('cstfield_%s', $customField->getData('magento_field'))] = 1;
            }
        }

        $values['export_customer_with_order'] = Mage::getStoreConfig('dolist/dolist_v8/export_customer_with_order');
        $values['calculatedfieds_mode'] = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_mode');
        $values['calculatedfieds_date'] = Mage::getStoreConfig('dolist/dolist_v8/calculatedfieds_date');
        $values['store_id'] = $storeId;

        $form->setValues($values);

        return parent::_prepareForm();
    }



    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('dolist')->__('Configuration');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('dolist')->__('Configuration');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    public function getMappingRowsString()
    {
        $result = array();
        $storeId = $this->getRequest()->getParam('store', 0);

        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
        $collection = Mage::getModel('dolist/dolistv8_customfields')
            ->getCollection()
            ->addFieldToFilter('scope_id', $storeId)
        ;

        foreach ($collection as $customField) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */

            if (!$customField->getData('magento_field')) {
                continue;
            }

            if($customField->getData('name') == 'email') {
                continue;
            }

            if (array_key_exists($customField->getData('name'), Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {
                continue;
            }

            if($customField->getData('type') != 'Varchar') {
                continue;
            }

            $row = new Varien_Object();
            $row->setData('_id', count($result));
            $row->setData('option_extra_attr_' . $customField->getData('magento_field'), 'selected="selected"');
            $row->setData('option_extra_attr_' . $customField->getData('name'), 'selected="selected"');

            $result[] = $row;
        }

        return $result;
    }

    public function getMappingRowsInt()
    {
        $result = array();
        $storeId = $this->getRequest()->getParam('store', 0);

        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
        $collection = Mage::getModel('dolist/dolistv8_customfields')
            ->getCollection()
            ->addFieldToFilter('scope_id', $storeId)
        ;

        foreach ($collection as $customField) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */

            if (!$customField->getData('magento_field')) {
                continue;
            }

            if (array_key_exists($customField->getData('name'), Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {
                continue;
            }

            if($customField->getData('type') != 'Integer') {
                continue;
            }

            $row = new Varien_Object();
            $row->setData('_id', count($result));
            $row->setData('option_extra_attr_' . $customField->getData('magento_field'), 'selected="selected"');
            $row->setData('option_extra_attr_' . $customField->getData('name'), 'selected="selected"');

            $result[] = $row;
        }

        return $result;
    }

    public function getMappingRowsDate()
    {
        $result = array();
        $storeId = $this->getRequest()->getParam('store', 0);

        /** @var Dolist_Net_Model_Mysql4_Dolistv8_Customfields_Collection $collection */
        $collection = Mage::getModel('dolist/dolistv8_customfields')
            ->getCollection()
            ->addFieldToFilter('scope_id', $storeId)
        ;

        foreach ($collection as $customField) {
            /** @var Dolist_Net_Model_Dolistv8_Customfields $customField */

            if (!$customField->getData('magento_field')) {
                continue;
            }

            if (array_key_exists($customField->getData('name'), Dolist_Net_Model_Dolistv8_Customfields::$coreFieldName)) {
                continue;
            }

            if($customField->getData('type') != 'Datetime') {
                continue;
            }

            $row = new Varien_Object();
            $row->setData('_id', count($result));
            $row->setData('option_extra_attr_' . $customField->getData('magento_field'), 'selected="selected"');
            $row->setData('option_extra_attr_' . $customField->getData('name'), 'selected="selected"');

            $result[] = $row;
        }

        return $result;
    }

    protected function _renderCellTemplateStr($columnName)
    {
        if (empty($this->_columnsStr[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columnsStr[$columnName];
        $inputName  = 'cstfieldStr[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
        ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
        (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
        (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }

    protected function _renderCellTemplateInt($columnName)
    {
        if (empty($this->_columnsInt[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columnsInt[$columnName];
        $inputName  = 'cstfieldInt[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
        ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
        (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
        (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }

    protected function _renderCellTemplateDate($columnName)
    {
        if (empty($this->_columnsDate[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columnsDate[$columnName];
        $inputName  = 'cstfieldDate[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
        ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
        (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
        (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    }

    protected function _getMagentoCustomerAttributeStrRenderer()
    {
        if (!$this->_magentoCustomerAttributeRendererStr) {
            $this->_magentoCustomerAttributeRendererStr = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_customerattributelist',
                '',
                array(
                    'is_render_to_js_template'  => true,
                    'backend_type'              => array('static', 'varchar', 'text')
                )
            );

            $this->_magentoCustomerAttributeRendererStr->setExtraParams('style="width:100%;"');
        }
        return $this->_magentoCustomerAttributeRendererStr;
    }

    protected function _getDolistv8AttributeStrRenderer()
    {
        if (!$this->_dolistv8AttributeRendererStr) {
            $this->_dolistv8AttributeRendererStr = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_attributelist',
                '',
                array(
                    'is_render_to_js_template' => true,
                    'custom_field_type'       => 'Varchar')
            );

            $this->_dolistv8AttributeRendererStr->setClass('dolistv8_custom_str_fields_select');
            $this->_dolistv8AttributeRendererStr->setExtraParams('style="width:100%;"');
        }
        return $this->_dolistv8AttributeRendererStr;
    }

    protected function _getMagentoCustomerAttributeIntRenderer()
    {
        if (!$this->_magentoCustomerAttributeRendererInt) {
            $this->_magentoCustomerAttributeRendererInt = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_customerattributelist',
                '',
                array(
                    'is_render_to_js_template'  => true,
                    'backend_type'              => array('int')
                )
            );

            $this->_magentoCustomerAttributeRendererInt->setExtraParams('style="width:100%;"');
        }
        return $this->_magentoCustomerAttributeRendererInt;
    }

    protected function _getDolistv8AttributeIntRenderer()
    {
        if (!$this->_dolistv8AttributeRendererInt) {
            $this->_dolistv8AttributeRendererInt = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_attributelist',
                '',
                array(
                    'is_render_to_js_template' => true,
                    'custom_field_type'       => 'Integer')
            );

            $this->_dolistv8AttributeRendererInt->setClass('dolistv8_custom_int_fields_select');
            $this->_dolistv8AttributeRendererInt->setExtraParams('style="width:100%;"');
        }
        return $this->_dolistv8AttributeRendererInt;
    }

    protected function _getMagentoCustomerAttributeDateRenderer()
    {
        if (!$this->_magentoCustomerAttributeRendererDate) {
            $this->_magentoCustomerAttributeRendererDate = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_customerattributelist',
                '',
                array(
                    'is_render_to_js_template'  => true,
                    'backend_type'              => array('datetime')
                )
            );

            $this->_magentoCustomerAttributeRendererDate->setExtraParams('style="width:100%;"');
        }
        return $this->_magentoCustomerAttributeRendererDate;
    }

    protected function _getDolistv8AttributeDateRenderer()
    {
        if (!$this->_dolistv8AttributeRendererDate) {
            $this->_dolistv8AttributeRendererDate = $this->getLayout()->createBlock(
                'dolist/adminhtml_system_config_dolistv8_attributelist',
                '',
                array(
                    'is_render_to_js_template' => true,
                    'custom_field_type'       => 'Datetime')
            );

            $this->_dolistv8AttributeRendererDate->setClass('dolistv8_custom_date_fields_select');
            $this->_dolistv8AttributeRendererDate->setExtraParams('style="width:100%;"');
        }
        return $this->_dolistv8AttributeRendererDate;
    }
}