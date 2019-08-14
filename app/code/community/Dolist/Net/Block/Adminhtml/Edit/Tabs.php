<?php

class Dolist_Net_Block_Adminhtml_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('dolist_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('dolist')->__('Dolist'));
    }

    protected function _beforeToHtml() {


        $this->addTab('dolist_tab_report', array(
            'label' => Mage::helper('dolist')->__('Report'),
            'title' => Mage::helper('dolist')->__('Report'),
            'content' => $this->getLayout()->createBlock('dolist/adminhtml_edit_tab_report')->toHtml(),
        ));

        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }

}