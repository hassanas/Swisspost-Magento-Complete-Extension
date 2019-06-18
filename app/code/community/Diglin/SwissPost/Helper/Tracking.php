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
 * Class Diglin_SwissPost_Helper_Tracking
 */
class Diglin_SwissPost_Helper_Tracking extends Mage_Core_Helper_Abstract
{
    const SHIPPING_NUMBER_LENGTH    = 18;

    const TRACKING_NUMBER_LENGTH    = 21;

    /**
     * @param $shippingNumber
     * @return string|false
     */
    public function formatShippingNumberToTrackingNumber($shippingNumber)
    {
        if (!strlen($shippingNumber) == self::SHIPPING_NUMBER_LENGTH) {
            return false;
        }
        $shippingCode       = substr($shippingNumber, 0, 2);
        $frankingLicense1   = substr($shippingNumber, 2, 2);
        $frankingLicense2   = substr($shippingNumber, 4, 6);
        $shippingId         = substr($shippingNumber, 10, 8);
        return $shippingCode .'.'. $frankingLicense1 .'.'. $frankingLicense2 .'.'. $shippingId;
    }

    /**
     * @param $trackingNumber
     * @return string|false
     */
    public function formatTrackingNumberToShippingNumber($trackingNumber)
    {
        if (!strlen($trackingNumber) == self::TRACKING_NUMBER_LENGTH) {
            return false;
        }
        $shippingCode       = substr($trackingNumber, 0, 2);
        $frankingLicense1   = substr($trackingNumber, 3, 2);
        $frankingLicense2   = substr($trackingNumber, 6, 6);
        $shippingId         = substr($trackingNumber, 13, 8);
        return $shippingCode . $frankingLicense1 . $frankingLicense2 . $shippingId;
    }
}