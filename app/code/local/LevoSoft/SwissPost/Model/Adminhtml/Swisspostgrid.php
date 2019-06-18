<?php
/**
 * @package      LevoSoft
 * @subpackage   SwissPost
 * @category     Checkout
 * @author     Hassan Ali Shahzad <levosoft786@gmail.com>
 */

/**
 * Class LevoSoft_SwissPost_Model_Adminhtml_Swisspostgrid
 */
class LevoSoft_SwissPost_Model_Adminhtml_Swisspostgrid extends Mage_Core_Model_Abstract
{

    /**
     * Generates and completes an order, reference: generateAndCompleteAction.
     *
     * @param $orderId
     * @return $this
     */
    public function generateAndCompleteOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $shipmentCollection = $order->getShipmentsCollection();

        if ($shipmentCollection->count() > 0 && !$order->getSwisspostLabelExists()) {
            $swisspostused = false;
            foreach ($shipmentCollection as $shipment) {
                $labelExists = Mage::helper('levosoft_swisspost')->trackingExists($orderId, $shipment->getIncrementId());
                //var_dump($labelExists);exit;
                if ($labelExists != false)
                    $labelPath = $labelExists;
                else
                    $labelPath = $this->_generateLabelAndReturnLabel($order, $shipment);

                if (empty($labelPath)) {
                    $message = Mage::helper('levosoft_swisspost')->__("Something went wrong while processing order %s, please check your error logs.", $order->getIncrementId());
                    Mage::getSingleton('core/session')->addError($message);
                    continue;
                } else {
                    $swisspostused = true;
                    //$locale = Mage::app()->getStore($order->getStoreId())->getConfig('general/locale/code');
                    //$localeCode = explode('_', $locale);
                    //$labelNameCode = explode('-', $labelName);
                    $shipment->setSwisspostLabelPath($labelPath);
                    //$shipment->setDpdTrackingUrl('<a target="_blank" href="' . "http://tracking.dpd.de/cgi-bin/delistrack?typ=32&lang=" . $localeCode[0] . "&pknr=" . $labelNameCode[1] . "&var=" . Mage::getStoreConfig('shipping/dpd_classic/userid') . '">' . Mage::helper('levosoft_swisspost')->__('Track this shipment') . '</a>');
                    //$transactionSave = Mage::getModel('core/resource_transaction')
                    //    ->addObject($shipment)
                    //    ->save();
                    $shipment->save();
                }
            }

            if ($swisspostused) {
                $order->addStatusHistoryComment(Mage::helper('levosoft_swisspost')->__('Shipped with SwissPost  generateLabel'), true);
                $order->setSwisspostLabelExists(1);
                $order->save();
                return true;
            } else {
                $message = Mage::helper('levosoft_swisspost')->__("The order with id %s has only none Swisspost shipments.", $order->getIncrementId());
                Mage::getSingleton('core/session')->addNotice($message);
                return false;
            }
        }
         elseif (!$order->getSwisspostLabelExists()) {


             $qty=array();

             $qty = Mage::helper('levosoft_swisspost')->calculateOrderQuantityToShip($order);

             if ($order->canShip()) {
                 $shipment = $order->prepareShipment($qty);
                 if ($shipment) {
                     $shipment->register();
                     $shipment->getOrder()->setIsInProcess(true);
                     try {
                         $transactionSave = Mage::getModel('core/resource_transaction')
                             ->addObject($shipment)
                             ->addObject($shipment->getOrder())
                             ->save();
                     } catch (Mage_Core_Exception $e) {
                         var_dump($e);
                     }

                 }
             }

                /* $shipment = $order->prepareShipment();
                $shipment->register();
                $weight = Mage::helper('levosoft_swisspost')->calculateTotalShippingWeight($shipment);
                $shipment->setTotalWeight($weight);*/

            $labelName = $this->_generateLabelAndReturnLabel($order, $shipment);
            if (!$labelName) {
                $message = Mage::helper('levosoft_swisspost')->__("Something went wrong while processing order %s, please check your error logs.", $order->getIncrementId());
                Mage::getSingleton('core/session')->addError($message);
                return false;
            } else {

                /*$explodeForCarrier = explode('_', $order->getShippingMethod(), 3);
                $locale = Mage::app()->getStore($order->getStoreId())->getConfig('general/locale/code');
                $localeCode = explode('_', $locale);
                $labelNameCode = explode('-', $labelName);
                $shipment->setDpdLabelPath($labelName . ".pdf");
                $shipment->setDpdTrackingUrl('<a target="_blank" href="' . "http://tracking.dpd.de/cgi-bin/delistrack?typ=32&lang=" . $localeCode[0] . "&pknr=" . $labelNameCode[1] . "&var=" . Mage::getStoreConfig('shipping/dpd_classic/userid') . '">' . Mage::helper('levosoft_swisspost')->__('Track this shipment') . '</a>');
                $order->setIsInProcess(true);
                $order->addStatusHistoryComment(Mage::helper('levosoft_swisspost')->__('Shipped with DPD generateLabelAndComplete'), true);
                $order->setDpdLabelExists(1);
                $tracker = Mage::getModel('sales/order_shipment_track')
                    ->setShipment($shipment)
                    ->setData('title', 'DPD')
                    ->setData('number', $labelName)
                    ->setData('carrier_code', $explodeForCarrier[0])
                    ->setData('order_id', $shipment->getData('order_id'));
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->addObject($tracker)
                    ->save();*/

                $shipment->setSwisspostLabelPath($labelName);
                $shipment->save();
                $order->addStatusHistoryComment(Mage::helper('levosoft_swisspost')->__('Shipped with SwissPost  generateLabel'), true);
                $order->setSwisspostLabelExists(1);
                $order->save();

                return true;
            }
        } else {
            $message = Mage::helper('levosoft_swisspost')->__("The order with id %s is not ready to be shipped or has already been shipped(in model).", $order->getIncrementId());
            Mage::getSingleton('core/session')->addNotice($message);
            return false;
        }
        return $this;
    }

    /**
     * Generates a shipment label and saves it on the harddisk.
     *
     * @param $order
     * @param $shipment
     * @return mixed
     */
    protected function _generateLabelAndReturnLabel($order, $shipment)
    {
        //var_dump($shipment);exit;
        /*
        $parcelshop = false;
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if (strpos($order->getShippingMethod(), 'parcelshop') !== false) {
            $parcelshop = true;
        }
        if ($parcelshop) {
            $recipient = array(
                'name1' => $billingAddress->getFirstname() . " " . $billingAddress->getLastname(),
                'name2' => $billingAddress->getCompany(),
                'street' => $billingAddress->getStreet(1) . " " . $billingAddress->getStreet(2),
                'country' => $billingAddress->getCountry(),
                'zipCode' => $billingAddress->getPostcode(),
                'city' => $billingAddress->getCity()
            );
        }
        else{
            $recipient = array(
                'name1' => $shippingAddress->getFirstname() . " " . $shippingAddress->getLastname(),
                'name2' => $shippingAddress->getCompany(),
                'street' => $shippingAddress->getStreet(1) . " " . $shippingAddress->getStreet(2),
                'country' => $shippingAddress->getCountry(),
                'zipCode' => $shippingAddress->getPostcode(),
                'city' => $shippingAddress->getCity()
            );
        }
        $labelWebserviceCallback = Mage::getSingleton('dpd/webservice')->getShippingLabel($recipient, $order, $shipment, $parcelshop);
*/

        /*if ($labelWebserviceCallback) {
            Mage::helper('levosoft_swisspost')->generatePdfAndSave($labelWebserviceCallback->parcellabelsPDF, 'orderlabels', $order->getIncrementId() . "-" . $labelWebserviceCallback->shipmentResponses->parcelInformation->parcelLabelNumber);
            return $order->getIncrementId() . "-" . $labelWebserviceCallback->shipmentResponses->parcelInformation->parcelLabelNumber;
        } else {
            return false;
        }*/

        $store = Mage::app()->getStore();
        $store->setConfig('levosoft_swisspost/sender/name2', '');

        $label = Mage::getModel('diglin_swisspost/label');
        $label->generateLabel($shipment);
        $path = str_replace(Mage::getBaseDir(),"",Mage::helper('diglin_swisspost/label')->getShipmentLabel($shipment,'path'));
        $extension = $store->getConfig('levosoft_swisspost/label/image_filetype');
        if (strpos($path, strtolower($extension)) !== false) {
            return $path;
        } else {
            return '';
        }
    }

    /**
     * Processes the undownloadable labels. (set mark and zip)
     *
     * @param $orderIds
     * @return bool|string
     */
    public function processUndownloadedLabels($orderIds)
    {
        $labelPdfArray = array();
        $i = 0;
        $err = false;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $exported = false;
            if (!$order->getSwisspostLabelExported()) {
                $shippingCollection = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($order)
                    ->load();

                if (count($shippingCollection)) {
                    foreach ($shippingCollection as $shipment) {
                        //if ($shipment->getDpdLabelPath() != "" && file_exists(Mage::getBaseDir('media') . "/dpd/orderlabels/" . $shipment->getDpdLabelPath()) && $shipment->getDpdLabelPath() != ".pdf") {
                        if ($shipment->getSwisspostLabelPath() != "" && file_exists(Mage::getBaseDir() . $shipment->getSwisspostLabelPath())) {
                        //if ($shipment->getSwisspostLabelPath() != "" && @getimagesize($shipment->getSwisspostLabelPath())) {
                            //$labelPdfArray[] = Mage::getBaseDir('media') . "/dpd/orderlabels/" . $shipment->getDpdLabelPath();
                            $labelPdfArray[] = Mage::getBaseDir() . $shipment->getSwisspostLabelPath();
                            $exported = true;
                        }
                    }
                    if ($exported) {
                        $order->setSwisspostLabelExported(1)->save();
                        $shipment->setSwisspostLabelExported(1)->save();
                    }
                }
            } else {
                $i++;
            }
        }

        if (!count($labelPdfArray)) {
            return false;
        }

        if ($i > 0) {
            $message = Mage::helper('levosoft_swisspost')->__('%s orders already had downloaded labels.', $i);
            Mage::getSingleton('core/session')->addNotice($message);
        } else {
            $message = Mage::helper('levosoft_swisspost')->__('All labels have been downloaded.');
            Mage::getSingleton('core/session')->addSuccess($message);
        }
        //return $this->_zipLabelPdfArray($labelPdfArray, Mage::getBaseDir('media') . "/dpd/orderlabels/undownloaded.zip", true);
        return $this->_zipLabelPdfArray($labelPdfArray, Mage::getBaseDir('media') . "/swisspost/orderlabels/undownloaded.zip", (file_exists(Mage::getBaseDir('media') . "/swisspost/orderlabels/undownloaded.zip"))?true:false);
    }

    /**
     * Zips the labels.
     *
     * @param array $files
     * @param string $destination
     * @param bool $overwrite
     * @return bool|string
     */
    protected function _zipLabelPdfArray($files = array(), $destination = '', $overwrite = false)
    {
        //var_dump($overwrite);exit;
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        $valid_files = array();
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                //if (@getimagesize($file)) {
                    $valid_files[] = $file;
                }
            }
        }

        if (count($valid_files)) {
            $zip = new ZipArchive();
            //if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE)) {
                foreach ($valid_files as $file) {
                    $zip->addFile($file, basename($file));
                }
                //return false;
            }

            $zip->close();
//var_dump($destination);exit;
            return $destination;
        } else {
            return false;
        }
    }

}