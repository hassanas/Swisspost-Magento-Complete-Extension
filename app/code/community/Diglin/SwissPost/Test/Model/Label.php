<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

define('MAGENTO_ROOT', dirname(__FILE__) . '/../../../../../../Mage.php');

require_once MAGENTO_ROOT;

Mage::app();

$shipmentId = '100512778';

$store = Mage::app()->getStore();

$store->setConfig('diglin_swisspost/sender/name2', '');

$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);

$label = Mage::getModel('diglin_swisspost/label');
$label->generateLabel($shipment);

$url = Mage::helper('diglin_swisspost/label')->getShipmentLabel($shipment);

$extension = $store->getConfig('diglin_swisspost/label/image_filetype');

if (strpos($url, strtolower($extension)) !== false) {
    echo sprintf('Url %s found', $url) . PHP_EOL;
} else {
    echo 'Label not generated or found' . PHP_EOL;
}
