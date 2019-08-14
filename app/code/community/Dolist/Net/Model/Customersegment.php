<?php
/**
 * Rewrite enterprise customer segment model only to add event prefix, then to plug observer
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_Customersegment extends Enterprise_CustomerSegment_Model_Segment
{
    protected $_eventPrefix = 'customersegment_segment';
}