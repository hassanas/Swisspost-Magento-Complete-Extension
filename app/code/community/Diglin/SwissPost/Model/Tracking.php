<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Model_Tracking extends Mage_Core_Model_Abstract
{
    /** @var Diglin_SwissPost_Helper_Tracking $_trackingHelper = null  */
    protected $_trackingHelper  = null;

    /**
     * @param string $shipmentIncrementId
     * @param string $carrierCode
     * @param string $shippingNumber
     */
    public function addTrackingCodeToShipment($shipmentIncrementId, $carrierCode, $shippingNumber)
    {
        $trackingNumber = $this->_formatTrackingNumber($shippingNumber);
        $trackingModel  = Mage::getModel('sales/order_shipment_api');
        $carrierTitle   = Mage::getStoreConfig('carriers/'. $carrierCode .'/title');
        $carrierTitle   = $carrierTitle != null ? $carrierTitle : $carrierCode;
        $trackingModel->addTrack(
            $shipmentIncrementId,
            $carrierCode,
            $carrierTitle,
            $trackingNumber
        );
    }

    private function _formatTrackingNumber($shippingNumber)
    {
        $trackingHelper = $this->_getTrackingHelper();
        $trackingNumber = $trackingHelper->formatShippingNumberToTrackingNumber($shippingNumber);
        return $trackingNumber;
    }

    /**
     * @return Diglin_SwissPost_Helper_Tracking
     */
    private function _getTrackingHelper()
    {
        if ($this->_trackingHelper == null) {
            $this->_trackingHelper = Mage::helper('diglin_swisspost/tracking');
        }
        return $this->_trackingHelper;
    }

}