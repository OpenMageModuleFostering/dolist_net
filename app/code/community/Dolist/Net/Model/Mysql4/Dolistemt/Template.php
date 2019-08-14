<?php
/**
 * Dolist-EMT Email template resource model
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Mysql4_Dolistemt_Template extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     * 
     * @return void
     */
    protected function _construct() 
    {
        $this->_init('dolist/dolistemt_template', 'id');
    }
}
