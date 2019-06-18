<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Helper_Config_ShippingMethod extends Mage_Core_Helper_Abstract
{

    public function getNormal()
    {
        $methods    = Mage::getStoreConfig('diglin_swisspost/shipping_method/normal');
        $methods    = explode(',', $methods);
        return $methods;
    }

    public function getPriority()
    {
        $methods    = Mage::getStoreConfig('diglin_swisspost/shipping_method/priority');
        $methods    = explode(',', $methods);
        return $methods;
    }

    public function getExpress()
    {
        $methods    = Mage::getStoreConfig('diglin_swisspost/shipping_method/express');
        $methods    = explode(',', $methods);
        return $methods;
    }

//    public function getEvening()
//    {
//        $methods    = Mage::getStoreConfig('diglin_swisspost/shipping_method/evening');
//        $methods    = explode(',', $methods);
//        return $methods;
//    }

}