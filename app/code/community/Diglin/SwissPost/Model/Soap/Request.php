<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Model_Soap_Request extends Mage_Core_Model_Abstract
{

    /** @var $_response null */
    protected $_response            = null;

    /** @var string */
    protected $_wsdlFile            = 'barcode_v2_2.wsdl';

    /** @var Diglin_SwissPost_Helper_Config_General $_generalConfigHelper = null */
    protected $_generalConfig = null;


    public function init()
    {
        $configHelper   = $this->_getGeneralConfig();
        try {
            $wsdlFile   = $this->_getWsdlFile();
            $config     = $configHelper->getConnectionData();
            $soapClient = new SoapClient($wsdlFile, $config);
        } catch (SoapFault $fault) {
            echo('Error in SOAP Initialization: '. $fault -> __toString() .'<br/>');
            exit;
        }
        return $soapClient;
    }

    private function _getWsdlFile()
    {
        return Mage::getModuleDir('etc', 'Diglin_SwissPost') . DS . $this->_wsdlFile;
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

}