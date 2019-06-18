<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Semantic
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_SwissPost_Model_Observer
 */
class Diglin_SwissPost_Model_Observer
{
    /** @var Diglin_SwissPost_Helper_Config_General $_generalConfig = null */
    protected $_generalConfig           = null;

    /** @var Diglin_SwissPost_Helper_ShippingMethod $_shippingMethodHelper = null */
    protected $_shippingMethodHelper    = null;

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Exception
     */
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment   = $observer->getEvent()->getShipment();

        $speCountriesAllow = Mage::getStoreConfigFlag('diglin_swisspost/shipping_method/sallowspecific');

        $availableCountries = null;
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            if (Mage::getStoreConfig('diglin_swisspost/shipping_method/specificcountry')) {
                $availableCountries = explode(',', Mage::getStoreConfig('diglin_swisspost/shipping_method/specificcountry'));
            }
        }

        if (is_array($availableCountries) && !in_array($shipment->getShippingAddress()->getCountryId(), $availableCountries)) {
            return $this;
        }

        if (Mage::registry('diglin_swisspost_observer_run')) {
            return $this;
        }

        Mage::register('diglin_swisspost_observer_run', 1);
        $generalConfig  = $this->_getGeneralConfig();
        if (!$generalConfig->isActive()) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order      = $shipment->getOrder();
        $allMethods     = $this->_getShippingMethodHelper()->getAllShippingMethods();
        $shippingMethod = $order->getShippingMethod(true);
        $carrierCode    = $shippingMethod->getCarrierCode();
        if (in_array($carrierCode, $allMethods) == false) {
            return $this;
        }
        $shipmentId = $shipment->getIncrementId();
        $label = Mage::getModel('diglin_swisspost/label');

        try {
            $label->generateLabel($shipmentId);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('diglin_swisspost')->__('Problem occurred with the SwissPost Label - %s', $e->getMessage()));
            throw $e;
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function diglinShipmentLabelCreateAfter(Varien_Event_Observer $observer)
    {

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment               = $observer->getEvent()->getShipment();

        if ($shipment->getSkipLabelCreateAfter()) {
            return;
        }

        $identCode              = $observer->getEvent()->getIdentCode();
        $shipmentIncrementId    = $shipment->getIncrementId();
        $shippingMethod         = $shipment->getOrder()->getShippingMethod(true);
        $carrierCode            = $shippingMethod->getCarrierCode();
        Mage::getModel('diglin_swisspost/tracking')->addTrackingCodeToShipment($shipmentIncrementId, $carrierCode, $identCode);

        return;
    }


    /**
     * @return Diglin_SwissPost_Helper_Config_General
     */
    private function _getGeneralConfig()
    {
        if ($this->_generalConfig == null) {
            $this->_generalConfig = Mage::helper('diglin_swisspost/config_general');
        }
        return $this->_generalConfig;
    }

    /**
     * @return Diglin_SwissPost_Helper_ShippingMethod
     */
    private function _getShippingMethodHelper()
    {
        if ($this->_shippingMethodHelper == null) {
            $this->_shippingMethodHelper = Mage::helper('diglin_swisspost/shippingMethod');
        }
        return $this->_shippingMethodHelper;
    }

}