<?php

class Dolist_Net_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'dolist';
        $this->_controller = 'adminhtml';

        $this->_updateButton('save', 'label', Mage::helper('dolist')->__('Save'));


        $this->removeButton('delete');
        $this->removeButton('reset');
    }

    public function getHeaderText() {
            return Mage::helper('dolist')->__('Dolist');
    }

}