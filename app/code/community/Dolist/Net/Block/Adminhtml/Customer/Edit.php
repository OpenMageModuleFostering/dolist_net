<?php
/**
 * Dolist-V8 rewrite block to add warning message when customer is loaded in back-office
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    /**
     * Only dispatch event to grab it in adminhtml observer
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $customerId = $this->getCustomerId();
        
        Mage::dispatchEvent(
            'dolist_status_adminhtml_customer_warning',
            array(
                'customer_id' => $customerId
            )
        );
    }
}
