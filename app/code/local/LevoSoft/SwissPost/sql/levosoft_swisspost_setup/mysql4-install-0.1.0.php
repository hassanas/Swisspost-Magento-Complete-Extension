<?php
/**
 * @package      LevoSoft
 * @subpackage   SwissPost
 * @category     Checkout
 * @author     Hassan Ali Shahzad <levosoft786@gmail.com>
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'swisspost_label_exported', "bool null default 0");
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'swisspost_label_exists', "bool null default 0");
$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'swisspost_label_exported', "bool null default 0");
$installer->getConnection()->addColumn($installer->getTable('sales/shipment'), 'swisspost_label_path', "varchar(255) null default ''");
$installer->endSetup();
