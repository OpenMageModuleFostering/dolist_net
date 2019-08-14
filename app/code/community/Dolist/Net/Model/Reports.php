<?php

class Dolist_Net_Model_Reports extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('dolist/reports');

    }

    /**
     * @param $max
     */
    public function start($max)
    {
        $now = new DateTime();
        $this->setData('started_at', $now->format(DateTime::W3C));
        $this->setData('progress_end', $max);
        try {
            $this->save();
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /**
     *
     */
    public function end($result = 'success')
    {
        $now = new DateTime();
        if('success' === $result) {
            $this->setData('progress_current', $this->getData('progress_end'));
        }
        $this->setData('result', $result);
        $this->setData('ended_at', $now->format(DateTime::W3C));

        try {
            $this->save();
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /**
     *
     */
    public function progress($progress)
    {
        $this->setData('progress_current', $progress);

        try {
            $this->save();
        } catch (Exception $ex) {
            Mage::log($ex->getMessage());
        }
    }

    /**
     * @param $message
     */
    public function log($message)
    {
        $message = sprintf('[%s] %s', date('Y-m-d H:i:s'), $message);

        $this->setData('last_logs', $message);
        $this->setData('logs', $this->getData('logs') . PHP_EOL . $message);

        Mage::helper('dolist')->logDebug($message);

        try {
            $this->save();
        } catch (Exception $ex) {
            Mage::helper('dolist')->logDebug($ex->__toString());
            Mage::logException($ex->__toString());
        }
    }


    protected function _beforeSave()
    {
        if ($this->hasDataChanges()) {
            $this->setData('updated_at', (date_create('now')->format('Y-m-d H:i:s')));
        }

        return parent::_beforeSave();
    }
} 