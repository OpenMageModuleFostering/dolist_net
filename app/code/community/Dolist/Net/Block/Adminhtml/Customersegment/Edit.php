<?php
/**
 * Dolist-V8 rewrite block to add export segment button
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_Customersegment_Edit extends Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit
{
    /**
     * Only dispatch event to grab it in adminhtml observer
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $segment = Mage::registry('current_customer_segment');
        Mage::dispatchEvent(
            'dolist_customersegment_export',
            array(
                'block'   => $this,
                'segment' => $segment
            )
        );
    }
}
