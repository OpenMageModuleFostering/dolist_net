<?php

class Dolist_Net_Block_Adminhtml_Edit_Tab_Report extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('dolist_report_grid');
        $this->setDefaultSort('created_at');
    }

    public function getMainButtonsHtml()
    {
        return '';
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Dolist_Net_Model_Mysql4_Reports_Collection $collection */
        $collection = Mage::getModel('dolist/reports')->getCollection();
        $collection->setOrder('id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('started_at', array(
            'header' => Mage::helper('catalog')->__('Started At'),
            'index' => 'started_at',
            'width' => '220',
            'align' => 'center',
            'filter' => false,
            'frame_callback' => array($this, 'decorateDate')
        ));

        $this->addColumn('ended_at', array(
            'header' => Mage::helper('catalog')->__('Ended At'),
            'index' => 'ended_at',
            'width' => '220',
            'align' => 'center',
            'filter' => false,
            'frame_callback' => array($this, 'decorateDate')
        ));

        $this->addColumn('type', array(
            'header' => $this->__('Type'),
            'align' => 'center',
            'index' => 'type',
            'width' => '220',
            'filter' => false,
            'frame_callback' => array($this, 'decorateType')
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Title'),
            'align' => 'left',
            'index' => 'name',
            'filter' => false,
        ));

        $this->addColumn('progress', array(
            'header' => $this->__('Status'),
            'width' => '120',
            'align' => 'center',
            'filter' => false,
            'frame_callback' => array($this, 'decorateProgress')
        ));
    }

    public function decorateDate($value, $row, $column, $isExport)
    {
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        if (!$value) {
            return ' - ';
        } else {
            return $coreHelper->formatDate($value, 'medium', 'medium');
        }
    }

    public function decorateType($value, $row, $column, $isExport)
    {
        switch ($value) {
            case 'export':
                return Mage::helper('dolist')->__('Export');
            default:
                return $value;
        }
    }

    public function decorateProgress($value, $row, $column, $isExport)
    {
        /** @var Dolist_Net_Model_Reports $row */

        if (!$row->getData('started_at') && ! $row->getData('ended_at')) {
            return '<span class="grid-severity-minor"><span>' . Mage::helper('dolist')->__('Initializing') . '</span></span>';
        }

        if ($row->getData('ended_at')) {
            if($row->getData('result') == 'success') {
                return '<span class="grid-severity-notice"><span>' . Mage::helper('dolist')->__('Completed') . '</span></span>';
            }
            else {
                return '<span class="grid-severity-critical"><span>' . Mage::helper('dolist')->__('Completed with error') . '</span></span>';
            }
        } else {
            $progressEnd = $row->getData('progress_end');

            if (is_numeric($progressEnd)) {
                $progressCurrent = $row->getData('progress_current');

                if ($progressCurrent == 0) {
                    return '<span class="grid-severity-major"><span>' . Mage::helper('dolist')->__('Starting') . '</span></span>';
                } else {
                    return '<span class="grid-severity-major"><span>' . Mage::helper('dolist')->__('Working') . ' ' . floor(($progressCurrent / $progressEnd) * 100) . '%</span></span>';
                }

            } else {
                return '<span class="grid-severity-major"><span>' . Mage::helper('dolist')->__('Working') . '</span></span>';
            }
        }

    }

    /**
     * Get row edit url
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/report', array('id' => $row->getId()));
    }

}