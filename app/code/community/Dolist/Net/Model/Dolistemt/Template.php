<?php
/**
 * Dolist-EMT Email template model
 * Dolist-EMT templates stored in Magento
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Dolistemt_Template extends Mage_Core_Model_Abstract
{
    /**
     * Constructor
     * 
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('dolist/dolistemt_template');
    }
    
    /**
     * Update stored Dolist-EMT template list if data retrieved from webservice is different
     * 
     * @param array $templateList Template list retrieved from webservice
     * 
     * @return bool True if stored template list has been updated
     */
    public function update($templateList)
    {
        $templateListUpdated = false;
        
        // Load stored template list
        $storedCollection   = $this->getCollection();
        $storedArray        = $storedCollection->toArray();
        
        // Transform collection to match arrays
        $transformedArray = array();

        // Retrieve stored values if any
        if ($storedArray['totalRecords'] > 0) {
            foreach ($storedArray['items'] as $storedTemplate) {
                $transformedArray[$storedTemplate['template_id']] = $storedTemplate['template_name'];
            }
        }

        // If template list has been modified on Dolist-EMT account, empty table and insert new values
        if ($transformedArray != $templateList) {

            // Delete old values
            foreach ($storedCollection as $storedItem) {
                $storedItem->delete();
            }

            // Insert new values
            foreach ($templateList as $id => $templatename) {
                // Instanciate new object and save it
                Mage::getModel('dolist/dolistemt_template')->setTemplateId($id)
                                                          ->setTemplateName($templatename)
                                                          ->save();
            }

            // Flag modifications need to be shown
            $templateListUpdated = true;
        }
        
        return $templateListUpdated;
    }
}