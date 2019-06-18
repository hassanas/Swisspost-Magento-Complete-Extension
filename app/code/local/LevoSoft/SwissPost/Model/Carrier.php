<?php
/**
 * @package      LevoSoft
 * @subpackage   SwissPost
 * @category     Checkout
 * @author     Hassan Ali Shahzad
<levosoft786@gmail.com>
 */


class LevoSoft_SwissPost_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'levosoftswisspost';

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice('10.00');
        $method->setCost('10.00');
        $result->append($method);
        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }

    public function isTrackingAvailable(){
        return true;
    }

    /**
     * Get tracking result object.
     *
     * @param string $tracking_number
     * @return Mage_Shipping_Model_Tracking_Result $tracking_result
     */
    public function getTrackingInfo($tracking_number)
    {//echo $tracking_number;exit;
        $tracking_result = $this->getTracking($tracking_number);

        if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result) {

            $trackings = $tracking_result->getAllTrackings();
            if (is_array($trackings) && count($trackings) > 0) {
                return $trackings[0];
            }
        }
        return false;
    }

    /**
     * Get tracking Url.
     *
     * @param string $tracking_number
     * @return Mage_Shipping_Model_Tracking_Result
     */
    public function getTracking($tracking_number)
    {
        $tracking_numberExploded = explode('.', $tracking_number);
        $tracking_result = Mage::getModel('shipping/tracking_result');
        $tracking_status = Mage::getModel('shipping/tracking_result_status');
        $localeExploded = explode('_', Mage::app()->getLocale()->getLocaleCode());
        $tracking_status->setCarrier($this->_code);
        $tracking_status->setCarrierTitle($this->getConfigData('title'));
        $tracking_status->setTracking($tracking_number);
        $tracking_status->addData(
            array(
                'status' => '<a target="_blank" href="' . "http://post.ch?lang=" . $localeExploded[0] . "&pknr=" . $tracking_numberExploded[1] . "&var=" . Mage::getStoreConfig('shipping/dpdclassic/userid') . '">' . Mage::helper('levosoft_swisspost')->__('Track this shipment(right now this feature not available)') . '</a>'
            )
        );
        $tracking_result->append($tracking_status);

        return $tracking_result;
    }
}
