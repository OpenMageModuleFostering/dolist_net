<?php

/**
 * Dolist block to display store switcher in Back Office
 *
 * @category  Dolist
 * @package   Dolist_Net
 * @copyright 2012 Dolist
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Dolist_Net_Block_Adminhtml_System_Config_Switcher extends Mage_Adminhtml_Block_System_Config_Switcher
{
    /**
     * Retrieve websites only
     * Used id is default store view id for each website
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');
        $curStore = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        $url = Mage::getModel('adminhtml/url');

        $options = array();
        $options['default'] = array(
            'label' => Mage::helper('adminhtml')->__('Default Config'),
            'url' => $url->getUrl('*/*/*', array('section' => $section)),
            'selected' => !$curStore,
            'style' => 'background:#ccc; font-weight:bold;',
        );

        foreach ($storeModel->getWebsiteCollection() as $website) {
            /** @var Mage_Core_Model_Website $website */

            $websiteShow = false;

            foreach ($website->getStores() as $store) {
                /** @var Mage_Core_Model_Store $store */


                $options['store_' . $store->getId()] = array(
                    'label' => $website->getName() . ' - ' . $store->getName(),
                    'url' => $url->getUrl('*/*/*', array('section' => $section, 'store' => $store->getId())),
                    'selected' => $curStore == $store->getId(),
                    'style' => 'padding-left:16px; background:#DDD; font-weight:bold;',
                );
            }
        }

        return $options;
    }

}
