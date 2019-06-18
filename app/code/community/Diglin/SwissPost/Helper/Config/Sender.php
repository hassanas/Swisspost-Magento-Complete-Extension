<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Helper_Config_Sender extends Mage_Core_Helper_Abstract
{

    /** return string */
    public function getName1()
    {
        return Mage::getStoreConfig('diglin_swisspost/sender/name1');
    }

    /** return string */
    public function getName2()
    {
        return Mage::getStoreConfig('diglin_swisspost/sender/name2');
    }

    /** return string */
    public function getStreet()
    {
        return Mage::getStoreConfig('diglin_swisspost/sender/street');
    }

    /** return string */
    public function getZip()
    {
        return trim(Mage::getStoreConfig('diglin_swisspost/sender/zip'));
    }

    /** return string */
    public function getCity()
    {
        return Mage::getStoreConfig('diglin_swisspost/sender/city');
    }

    /** return string */
    public function getCountry()
    {
        return Mage::getStoreConfig('diglin_swisspost/sender/country');
    }

    /** return string */
    public function getDomicilePostOffice()
    {
        return Mage::getStoreConfig('diglin_swisspost/sender/domicile_post_office');
    }

}