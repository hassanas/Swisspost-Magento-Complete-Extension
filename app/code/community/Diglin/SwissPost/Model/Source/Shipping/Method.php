<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_SwissPost_Model_Source_Shipping_Method
 */
class Diglin_SwissPost_Model_Source_Shipping_Method
{

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

    public function toOptionArray()
    {
        $methods    = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options    = array();
        foreach ($methods as $code => $method) {
            if(!$title = Mage::getStoreConfig("carriers/$code/title")) {
                $title = $code;
            }
            $options[] = array('value' => $code, 'label' => $title . " ($code)");

        }
        return $options;
    }

}