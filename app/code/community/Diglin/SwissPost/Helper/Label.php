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
 * Class Diglin_SwissPost_Helper_Label
 */
class Diglin_SwissPost_Helper_Label extends Mage_Core_Helper_Abstract
{
    /** @var string $_language */
    protected $_language = 'de';

    /**
     * @var Diglin_SwissPost_Helper_Config_Label
     */
    protected $_labelConfig = null;

    /**
     * @var Diglin_SwissPost_Helper_Config_General
     */
    protected $_generalConfig = null;

    /**
     * @var Diglin_SwissPost_Helper_Config_Sender
     */
    protected $_senderConfig = null;

    protected $_label_size_a5 = array('w' => 595, 'h' => 420);

    protected $_label_size_a6 = array('w' => 420, 'h' => 298);

    protected $_label_size_a7 = array('w' => 298, 'h' => 210);

    /**
     * @param $shipmentId
     * @param $trackingCode
     * @return string
     */
    public function getLabelPath($shipmentId, $trackingCode)
    {
        $labelConfig    = $this->_getLabelConfig();
        $folder         = $labelConfig->getImageOutputFolder();
        $fileType       = strtolower($labelConfig->getImageFileType());
        $outputDir      = rtrim(Mage::getBaseDir('media') . DS . $folder, '/');

        $io = new Varien_Io_File();
        $io->mkdir($outputDir, 0775);

        $fileName = $shipmentId . '_' . $trackingCode . '.' . $fileType;

        return $outputDir . DS . $fileName;
    }

    /**
     * @param $shipmentId
     * @param $trackingCode
     * @return string
     */
    public function getLabelUrl($shipmentId, $trackingCode)
    {
        if (!file_exists($this->getLabelPath($shipmentId, $trackingCode))) {
            return false;
        }

        $labelConfig    = $this->_getLabelConfig();
        $folder         = $labelConfig->getImageOutputFolder();
        $fileType       = strtolower($labelConfig->getImageFileType());
        $outputDir      = rtrim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $folder, '/');
        $fileName       = $shipmentId  .'_'. $trackingCode .'.'. $fileType;

        return $outputDir . DS . $fileName;
    }

    /**
     * @return array
     */
    public function getLabelSize()
    {
        $labelConfig = $this->_getLabelConfig();
        $labelLayout = $labelConfig->getLayout();
        switch ($labelLayout) {
            case 'A5':
                return $this->_label_size_a5;
                break;
            case 'A7':
                return $this->_label_size_a7;
                break;
            case 'A6':
            default:
                return $this->_label_size_a6;
                break;
        }
    }

    /**
     * @return Diglin_SwissPost_Helper_Config_Label|Mage_Core_Helper_Abstract
     */
    private function _getLabelConfig()
    {
        if ($this->_labelConfig == null) {
            $this->_labelConfig = Mage::helper('diglin_swisspost/config_label');
        }

        return $this->_labelConfig;
    }

    /**
     * Only the label of the first track collection of a shipment will be returned
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param string $type
     * @return null|string
     */
    public function getShipmentLabel(Mage_Sales_Model_Order_Shipment $shipment, $type = 'url')
    {
        $shippingHelper = Mage::helper('diglin_swisspost/shippingMethod');

        $order          = $shipment->getOrder();

        $allMethods     = $shippingHelper->getAllShippingMethods();
        $shippingMethod = $order->getShippingMethod(true);
        $carrierCode    = $shippingMethod->getCarrierCode();

        if (!in_array($carrierCode, $allMethods)) {
            return null;
        }

        $shipmentId     = $shipment->getIncrementId();
        $tracks         = $shipment->getAllTracks();
        $trackingCode   = null;

        foreach ($tracks as $track) {
            $trackingCode   = $track->getNumber();
            break;
        }

        $trackingCode   = Mage::helper('diglin_swisspost/tracking')->formatTrackingNumberToShippingNumber($trackingCode);

        if ($trackingCode) {
            switch ($type) {
                case 'url':
                    return $this->getLabelUrl($shipmentId, $trackingCode);
                    break;
                case 'path':
                    return $this->getLabelPath($shipmentId, $trackingCode);
                    break;
            }
        }

        return null;
    }
}