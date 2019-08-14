<?php
/**
 * Dolist source chooser to select custom field
 * Factorized class for String, Int and Date
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Model_System_Config_Source_Dolistv8_Customfield
{
    /**
     * Return Dolist-V8 CustomStr, CustomInt or CustomDate fields
     *
     * @param string $label Label, can be 'CustomStr', 'CustomInt', 'CustomDate'
     * @param int    $size  Size for this label
     * 
     * @return array
     */
    public function toOptionArray($label, $size)
    {
        $options = array();
        for ($i = 1; $i <= $size; $i++) {
            $fieldname = $label . $i;
            $options[] = array('value' => $fieldname, 'label' => $fieldname);
        }
        
        return $options;
    }
}
