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
 * Class Diglin_SwissPost_Model_Label
 */
class Diglin_SwissPost_Model_Label extends Mage_Core_Model_Abstract
{

    /** @var  $_label = null */
    protected $_label = null;

    /** @var Mage_Sales_Model_Order_Shipment $_shipment = null */
    protected $_shipment = null;

    /** @var integer $_indentCode = null */
    protected $_identCode = null;

    /** @var string $_language */
    protected $_language = 'de';

    /** @var Diglin_SwissPost_Helper_Label $_labelHelper = null */
    protected $_labelHelper = null;

    /** @var Diglin_SwissPost_Helper_ShippingMethod $_shippingMethodHelper = null */
    protected $_shippingMethodHelper = null;

    /** @var Diglin_SwissPost_Helper_Util $_utilHelper = null */
    protected $_utilHelper = null;

    /** @var Diglin_SwissPost_Helper_Config_Label $_labelConfig = null */
    protected $_labelConfig = null;

    /** @var Diglin_SwissPost_Helper_Config_General $_generalConfig = null */
    protected $_generalConfig = null;

    /** @var Diglin_SwissPost_Helper_Config_Sender $_senderConfig = null */
    protected $_senderConfig = null;

    protected $_customRecipient = null;

    /**
     * @var array|null
     */
    protected $_shippingCodes;

    /**
     * @param string $shipmentId
     */
    public function generateLabel($shipmentId)
    {
        $this->_setShipment($shipmentId);
        $utilHelper = $this->_getUtilHelper();
        /** @var SOAP_Client $soapClient */
        $soapClient = Mage::getModel('diglin_swisspost/soap_request')->init();
        $data       = $this->_collectData();
        $response   = $soapClient->GenerateLabel($data);

        foreach ($utilHelper->getElements($response->Envelope->Data->Provider->Sending->Item) as $item) {
            $errors = empty($item->Errors) == false ? $item->Errors : null;
            if ($errors) {
                $this->handleErrors($errors);
            }

            $this->_setLabel($item->Label);
            $this->_setIdentCode($item->IdentCode);
            $this->saveAsImage();
            Mage::dispatchEvent(
                'diglin_shipment_label_create_after',
                [
                    'shipment'          => $this->_getShipment(),
                    'ident_code'        => $item->IdentCode,
                    'label_binary_data' => $item->Label
                ]
            );

            return;
        }
    }

    /**
     * @return string
     */
    public function generateRmaLabel()
    {
        $shippingMethods = explode(',', Mage::helper('diglin_swisspost/config_rma')->getRmaMethod());
        array_walk($shippingMethods, function(&$value){
            $value = trim($value);
        });

        $this->_setShippingCodes($shippingMethods);

        $utilHelper = $this->_getUtilHelper();
        /** @var SOAP_Client $soapClient */
        $soapClient = Mage::getModel('diglin_swisspost/soap_request')->init();
        $data = $this->_collectData();
        $response = $soapClient->GenerateLabel($data);
        foreach ($utilHelper->getElements($response->Envelope->Data->Provider->Sending->Item) as $item) {
            $errors = empty($item->Errors) == false ? $item->Errors : null;
            if ($errors) {
                $this->handleErrors($errors);
            }
            $this->_setLabel($item->Label);
            $this->_setIdentCode($item->IdentCode);

            return $this->saveAsImage();
        }
    }

    /**
     * @return string
     */
    public function saveAsImage()
    {
        $label          = $this->_getLabel();
        $identCode      = $this->_getIdentCode();
        $sendingId      = $this->_getSendingId(); // shipment id
        $labelHelper    = $this->_getLabelHelper();
        $outputFile     = $labelHelper->getLabelPath($sendingId, $identCode);
        file_put_contents($outputFile, $label);

        return $outputFile;
    }

    /**
     * @param $shipmentId
     * @return Mage_Sales_Model_Order_Shipment
     */
    private function _setShipment($shipmentId)
    {
        if ($this->_shipment == null) {
            if ($shipmentId instanceof Mage_Sales_Model_Order_Shipment) {
                $this->_shipment = $shipmentId;
            } else {
                $this->_shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);
            }
        }

        return $this->_shipment;
    }

    /**
     * @return Mage_Sales_Model_Order_Shipment
     */
    private function _getShipment()
    {
        return $this->_shipment;
    }

    /**
     * @return array
     */
    private function _collectData()
    {
        $data = array(
            'Language' => $this->_getLanguage(),
            'Envelope' => array(
                'LabelDefinition' => $this->_getLabelDefinition(),
                'FileInfos'       => $this->_getFileInfo(),
                'Data'            => array(
                    'Provider' => array(
                        'Sending' => array(
                            'SendingId' => $this->_getSendingId(),
                            'Item'      => array(
                                array(
                                    'ItemId'     => $this->_getItemId(),
                                    'Recipient'  => $this->_getRecipient(),
                                    'Attributes' => $this->_getAttributes(),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        return $data;
    }

    /**
     * @return null
     */
    private function _getLabel()
    {
        return $this->_label;
    }

    /**
     * @param $label
     */
    private function _setLabel($label)
    {
        $this->_label = $label;
    }

    /**
     * @return int
     */
    private function _getIdentCode()
    {
        return $this->_identCode;

    }

    /**
     * @param $identCode
     */
    private function _setIdentCode($identCode)
    {
        $this->_identCode = $identCode;
    }

    /**
     * @return string
     */
    private function _getLanguage()
    {
        return $this->_language;
    }

    /**
     * @return array
     */
    private function _getLabelDefinition()
    {
        $labelConfig = $this->_getLabelConfig();
        $data = array(
            'LabelLayout'     => $labelConfig->getLayout(),
            'PrintAddresses'  => 'RecipientAndCustomer',
            'ImageFileType'   => $labelConfig->getImageFileType(),
            'ImageResolution' => $labelConfig->getImageResolution(),
            'PrintPreview'    => false,
        );

        return $data;
    }

    /**
     * @return array
     */
    private function _getFileInfo()
    {
        $generalConfig = $this->_getGeneralConfig();
        $senderConfig = $this->_getSenderConfig();
        $data = array(
            'FrankingLicense' => $generalConfig->getFrankingLicense(),
            'PpFranking'      => false,
            'Customer'        => array(
                'Name1'              => $senderConfig->getName1(),
                'Name2'              => $senderConfig->getName2(),
                'Street'             => $senderConfig->getStreet(),
                // 'POBox'      => 'Postfach 600',
                'ZIP'                => $senderConfig->getZip(),
                'City'               => $senderConfig->getCity(),
                'Country'            => $senderConfig->getCountry(),
                // 'Logo'       => $logo_binary_data,
                // 'LogoFormat' => 'GIF',
                'DomicilePostOffice' => $senderConfig->getDomicilePostOffice(),
            ),
            'CustomerSystem'  => 'PHP Client System'
        );

        return $data;
    }

    /**
     * @return mixed|null
     */
    private function _getItemId()
    {
        $shipmentId = null;
        $shipment = $this->_getShipment();
        if ($shipment) {
            $shipmentId = $shipment->getEntityId();
        }

        return $shipmentId;
    }

    /**
     * @return null|string
     */
    private function _getSendingId()
    {
        $sendingId = null;
        $shipment = $this->_getShipment();
        if ($shipment) {
            $sendingId = $shipment->getIncrementId();
        }

        return $sendingId;
    }


    /**
     * @return array
     */
    private function _getRecipient()
    {
        $name1 = $name2 = $street1 = $street2 = $addresssuffix = $zip = $city = $email = null;

        $shipment = $this->_getShipment();
        if ($shipment) {
            $shippingAddress = $shipment->getShippingAddress();

            $name1 = mb_strcut($shippingAddress->getName(), 0, 35);
            $name2 = mb_strcut($shippingAddress->getCompany(), 0, 35);
            $street1 = mb_strcut($shippingAddress->getStreet1(), 0, 35);
            $street2 = mb_strcut($shippingAddress->getStreet2(), 0, 35);
            $zip = mb_strcut($shippingAddress->getPostcode(), 0, 10);
            $city = mb_strcut($shippingAddress->getCity(), 0, 35);
            $email = mb_strcut($shippingAddress->getEmail(), 0, 160);
        } else if ($this->getCustomRecipient()) {
            $customRecipient = $this->getCustomRecipient();

            $name1 = $customRecipient->getName1();
            $name2 = $customRecipient->getName2();
            $street1 = $customRecipient->getStreet1();
            $street2 = $customRecipient->getStreet2();
            $zip = $customRecipient->getZip();
            $city = $customRecipient->getCity();
        } else {
            // For RMA or non declared recipients
            $senderConfig = $this->_getSenderConfig();

            $name1 = $senderConfig->getName1();
            $name2 = $senderConfig->getName2();
            $street1 = $senderConfig->getStreet();
            $zip = $senderConfig->getZip();
            $city = $senderConfig->getCity();
        }

        $data = array(
            //'PostIdent'   => 'IdentCodeUser',
            //'Title'       => 'Frau',
            'Name1'         => $name1,
            'Name2'         => $name2,
            'Street'        => $street1,
            //'AddressSuffix' => $street2,
            //'HouseNo'     => '21',
            //'FloorNo'     => '1',
            //'MailboxNo'   => '1111',
            'ZIP'           => $zip,
            'City'          => $city,
            //'Country'     => 'CH',
            //'Phone'       => '0313381111', // für ZAW3213
            //'Mobile'      => '0793381111',
            'Email'         => $email,
//            'LabelAddress'  => array(
//                'LabelLine' => array(
//                    $shippingAddress->getStreet1(),
//                    $shippingAddress->getStreet2(),
//                    //$shippingAddress->getPostcode() .' '. $shippingAddress->getCity(),
//                )
//            )
        );

        return $data;
    }


    /**
     * @return array
     */
    private function _getAttributes()
    {
        $generalConfig = $this->_getGeneralConfig();
        $data = array(
            'PRZL'     => $this->_getShippingCodes(),
            'ProClima' => $generalConfig->getProClima(),
            // Cash on delivery amount in CHF for service 'N':
            //'Amount' => 12.5,
            //'FreeText' => 'Freitext',
            // 'DeliveryDate' => '2010-06-19',
            // 'ParcelNo' => 2,
            // 'ParcelTotal' => 5,
            // 'DeliveryPlace' => 'Vor der Haustüre',
        );

        return $data;
    }

    /**
     * @return array
     */
    private function _getShippingCodes()
    {
        $shipment = $this->_getShipment();

        if (!$this->_shippingCodes && $shipment) {
            $shippingHelper = $this->_getShippingMethodHelper();
            $shippingMethod = $shipment->getOrder()->getShippingMethod(true);
            $carrierCode = $shippingMethod->getCarrierCode();
            $this->_shippingCodes = $shippingHelper->getShippingMethodCodes($carrierCode);
        }

        return $this->_shippingCodes;
    }

    /**
     * @param array $codes
     * @return $this
     */
    private function _setShippingCodes(array $codes)
    {
        $this->_shippingCodes = $codes;

        return $this;
    }

    /**
     * @return Diglin_SwissPost_Helper_Config_Label
     */
    private function _getLabelConfig()
    {
        if ($this->_labelConfig == null) {
            $this->_labelConfig = Mage::helper('diglin_swisspost/config_label');
        }

        return $this->_labelConfig;
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
     * @return Diglin_SwissPost_Helper_Config_Sender
     */
    private function _getSenderConfig()
    {
        if ($this->_senderConfig == null) {
            $this->_senderConfig = Mage::helper('diglin_swisspost/config_sender');
        }

        return $this->_senderConfig;
    }

    /**
     * @return Diglin_SwissPost_Helper_Label
     */
    private function _getLabelHelper()
    {
        if ($this->_labelHelper == null) {
            $this->_labelHelper = Mage::helper('diglin_swisspost/label');
        }

        return $this->_labelHelper;
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

    /**
     * @return Diglin_SwissPost_Helper_Util
     */
    private function _getUtilHelper()
    {
        if ($this->_utilHelper == null) {
            $this->_utilHelper = Mage::helper('diglin_swisspost/util');
        }

        return $this->_utilHelper;
    }

    /**
     * @param stdClass $errors
     */
    private function handleErrors(stdClass $errors)
    {
        Mage::throwException(
            sprintf(
                'SwissPost Error: %s %s',
                $errors->Error->Code,
                $errors->Error->Message
            )
        );
    }

    /**
     * @return Diglin_SwissPost_Model_Recipient|null
     */
    public function getCustomRecipient()
    {
        return $this->_customRecipient;
    }

    /**
     * @param Diglin_SwissPost_Model_Recipient $customRecipient
     * @return $this
     */
    public function setCustomRecipient(Diglin_SwissPost_Model_Recipient $customRecipient)
    {
        $this->_customRecipient = $customRecipient;

        return $this;
    }
}
