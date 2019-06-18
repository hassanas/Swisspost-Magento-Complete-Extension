<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Helper_Config_General extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isActive()
    {
        $active = Mage::getStoreConfig('diglin_swisspost/general/active');
        if ($active === '1') {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getConnectionData()
    {
        return array(
            'location'  => $this->_getSoapEndpoint(),
            'login'     => $this->_getUsername(),
            'password'  => $this->_getPassword(),
        );
    }

    /**
     * @return mixed
     */
    public function getFrankingLicense()
    {
        return Mage::getStoreConfig('diglin_swisspost/general/franking_license');
    }

    /**
     * @return mixed
     */
    private function _getUsername()
    {
        return Mage::getStoreConfig('diglin_swisspost/general/username');
    }

    /**
     * @return mixed
     */
    private function _getPassword()
    {
        return Mage::getStoreConfig('diglin_swisspost/general/password');
    }

    /**
     * @return mixed
     */
    private function _getSoapEndpoint()
    {
        return Mage::getStoreConfig('diglin_swisspost/general/soap_endpoint');
    }

    /**
     * @return bool
     */
    public function getProClima()
    {
        $proClima = Mage::getStoreConfig('diglin_swisspost/general/pro_clima');
        if ($proClima === '1') {
            return true;
        }
        return false;
    }

}