<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_SwissPost_Model_Source_Rma_Method
 */
class Diglin_SwissPost_Model_Source_Rma_Method
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $output = array();
        foreach ($this->toOptionArray() as $key => $value) {
            $output[] = array(
                'value' => $key,
                'label' => $value,
            );
        }

        return $output;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @todo automate the list into the config.xml

        $helper = Mage::helper('diglin_swisspost');
        $options = [
            ['value' => Diglin_SwissPost_Helper_ShippingMethod::POSTPAC_PRIORITY_GAS, 'label' => $helper->__('PostPac Priority GAS')],
            ['value' => Diglin_SwissPost_Helper_ShippingMethod::POSTPAC_ECONOMY_GAS, 'label' => $helper->__('PostPac Economy GAS')],
        ];

        $transport = new Varien_Object();
        Mage::dispatchEvent('diglin_swisspost_rma_methods', array('transport' => $transport,'options' => $options));

        if ($transport->getData('options')) {
            $options = $transport->getData('transport');
        }

        return $options;
    }

}