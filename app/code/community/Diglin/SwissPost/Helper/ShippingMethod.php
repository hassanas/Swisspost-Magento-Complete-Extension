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
 * Class Diglin_SwissPost_Helper_ShippingMethod
 */
class Diglin_SwissPost_Helper_ShippingMethod extends Mage_Core_Helper_Abstract
{

    /** @var Diglin_SwissPost_Helper_Config_ShippingMethod $_shippingConfig = null */
    protected $_shippingMethodConfig    = null;

    const NORMAL_DELIVERY_CODE          = 'ECO';
    const PRIORITY_DELIVERY_CODE        = 'PRI';
    const EXPRESS_DELIVERY_CODE         = 'SEM';
    const POSTPAC_PRIORITY_GAS          = 'GAS,PRI';
    const POSTPAC_ECONOMY_GAS           = 'GAS,ECO';

    public function getShippingMethodCodes($carrierCode)
    {
        $codes  = array();
        if ($this->_isExpressDelivery($carrierCode)) {
            $codes[]    = self::EXPRESS_DELIVERY_CODE;
        } else if ($this->_isPriorityDelivery($carrierCode)) {
            $codes[]    = self::PRIORITY_DELIVERY_CODE;
        } else {
            $codes[]    = self::NORMAL_DELIVERY_CODE;
        }
        return $codes;
    }

    public function getAllShippingMethods()
    {
        $methods                = array();
        $shippingMethodConfig   = $this->_getShippingMethdodConfig();
        $normalMethods          = $shippingMethodConfig->getNormal();
        $priorityMethods        = $shippingMethodConfig->getPriority();
        $expressMethods         = $shippingMethodConfig->getExpress();
        $methods                = array_merge($methods, $normalMethods, $priorityMethods, $expressMethods);
        return $methods;
    }

    /**
     * @param string $carrierCode
     * @return boolean
     */
    private function _isPriorityDelivery($carrierCode)
    {
        $shippingMethodConfig   = $this->_getShippingMethdodConfig();
        $priorityMethods        = $shippingMethodConfig->getPriority();
        if (in_array($carrierCode, $priorityMethods)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $carrierCode
     * @return boolean
     */
    private function _isExpressDelivery($carrierCode)
    {
        $shippingMethodConfig   = $this->_getShippingMethdodConfig();
        $expressMethods         = $shippingMethodConfig->getExpress();
        if (in_array($carrierCode, $expressMethods)) {
            return true;
        }
        return false;
    }

    /**
     * @return Diglin_SwissPost_Helper_Config_ShippingMethod
     */
    private function _getShippingMethdodConfig()
    {
        if ($this->_shippingMethodConfig == null) {
            $this->_shippingMethodConfig = Mage::helper('diglin_swisspost/config_shippingMethod');
        }
        return $this->_shippingMethodConfig;
    }


}