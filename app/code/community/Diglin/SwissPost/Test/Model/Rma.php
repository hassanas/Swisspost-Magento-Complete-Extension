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

$store = Mage::app()->getStore();

$store->setConfig('diglin_swisspost/rma/active', 1);
$store->setConfig('diglin_swisspost/rma/method', Diglin_SwissPost_Helper_ShippingMethod::POSTPAC_PRIORITY_GAS);
$store->setConfig('diglin_swisspost/label/layout', 'A5');
$store->setConfig('diglin_swisspost/sender/name2', '');

$label = Mage::getModel('diglin_swisspost/label');

$url = $label->generateRmaLabel();

$extension = $store->getConfig('diglin_swisspost/label/image_filetype');

if (strpos($url, strtolower($extension)) !== false) {
    echo sprintf('Url %s found', $url) . PHP_EOL;
} else {
    echo 'Label not generated or found' . PHP_EOL;
}
