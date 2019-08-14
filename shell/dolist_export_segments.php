<?php
require_once 'abstract.php';

/**
 * Dolist Segment Export Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 */
class Mage_Shell_Segments_Export extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($ids = $this->getArg('ids')) {
            $this->_getHelper()->logDebug('Starting Segments export');
            $process = new Mage_Index_Model_Process();
            $process->setId("segment_export");
            if($process->isLocked()){
                $this->_getHelper()->logDebug('segment_export process is already locked');
                return;
            }
            $process->lockAndBlock();
            $this->_getHelper()->logDebug('segment_export was not locked, but now it is');

            //Récuperation des ids des segments
            $idList = explode(',',$ids);
            $idList = array_filter($idList, 'is_numeric');

            $existingSegmentIds = Mage::getModel('enterprise_customersegment/segment')->getCollection()->getAllIds();
            $exportSegments = TRUE;

            if (!is_array($existingSegmentIds)) {
                $exportSegments = FALSE;
                $msg = 'No segments defined yet.';
                $this->_getHelper()->logError($msg);
            } else {
                foreach($idList as $segmentId) {
                    if (!in_array($segmentId, $existingSegmentIds)) {
                        $exportSegments = FALSE;
                        $msg = sprintf('Segment Id %s does not exist.',$segmentId);
                        $this->_getHelper()->logError($msg);
                    }
                }
            }

            if ($exportSegments) {
                foreach($idList as $segmentId) {
                    $this->_getHelper()->exportSegment($segmentId);
                }
            } else {
                $msg = 'No segment were exported, please check segment ids provided';
                $this->_getHelper()->logError($msg);
            }
            $process->unlock();
            $this->_getHelper()->logDebug('Ending Segments export');

        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f dolist_export_segments.php -- [options]

  --ids segmentId1,segmentId2     exports the list of segments defined by their id (comma separated)
  help          This help

USAGE;
    }


    /**
     * Retrieve model helper
     *
     * @return Dolist_Net_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('dolist');
    }
}

$shell = new Mage_Shell_Segments_Export();
$shell->run();
