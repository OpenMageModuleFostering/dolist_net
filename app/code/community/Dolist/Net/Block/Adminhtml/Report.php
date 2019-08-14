<?php

class Dolist_Net_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Container
{
    private $model;

    protected function _afterToHtml($html)
    {
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        $progressCurrent = $this->getReport()->getData('progress_current');
        $progressEnd = $this->getReport()->getData('progress_end');

        return '
<div class="content-header">
    <p class="form-buttons"><button title="Back" type="button" class="scalable back" onclick="setLocation(\'' . Mage::helper('adminhtml')->getUrl('*/*/') . '\')" style=""><span><span><span>Back</span></span></span></button></p>
</div>
<div class="entry-edit">
    <div class="entry-edit-head"><h4 class="icon-head head-customer-view">' . Mage::helper('dolist')->__('Report Information') . '</h4></div>
    <fieldset>
        <table cellspacing="2" class="box-left">
            <tbody>
                <tr>
                    <td><strong>' . Mage::helper('dolist')->__('Type:') . '</strong></td>
                    <td>' . Mage::helper('dolist')->__($this->getReport()->getData('type')) . '</td>
                </tr>
                <tr>
                    <td><strong>' . Mage::helper('dolist')->__('Name:') . '</strong></td>
                    <td>' . $this->getReport()->getData('name') . '</td>
                </tr>
                <tr>
                    <td><strong>' . Mage::helper('dolist')->__('Started At:') . '</strong></td>
                    <td>' . ($this->getReport()->getData('started_at') ? $coreHelper->formatDate($this->getReport()->getData('started_at'), 'medium', 'medium') : ' - ') . '</td>
                </tr>
                <tr>
                    <td><strong>' . Mage::helper('dolist')->__('Ended At:') . '</strong></td>
                    <td>' . ($this->getReport()->getData('ended_at') ? $coreHelper->formatDate($this->getReport()->getData('ended_at'), 'medium', 'medium') : ' - ') . '</td>
                </tr>
                <tr>
                    <td><strong>' . Mage::helper('dolist')->__('Result:') . '</strong></td>
                    <td>' . $this->getReport()->getData('result') . ' </td>
                </tr>
                <tr>
                    <td><strong>' . Mage::helper('dolist')->__('Progression:') . '</strong></td>
                    <td>' . ($progressEnd != 0 ? (floor(($progressCurrent / $progressEnd) * 100)) : '-') . '%  (' . ($progressCurrent ? $progressCurrent : '-') . '/' . ($progressEnd ? $progressEnd : '-') . ')  </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>
    <h4>Logs</h4>
    <pre>' . $this->getReport()->getData('logs') . '</pre>';
    }

    /**
     * @return Dolist_Net_Model_Reports
     */
    protected function getReport()
    {
        if (!$this->model) {
            $this->model = Mage::getModel('dolist/reports');
            if ($this->getRequest()->getParam('id')) {
                $this->model = $this->model->load($this->getRequest()->getParam('id'));
            }
        }

        return $this->model;
    }
} 