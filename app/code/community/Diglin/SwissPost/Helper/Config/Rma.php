<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_SwissPost_Helper_Config_Rma
 */
class Diglin_SwissPost_Helper_Config_Rma extends Mage_Core_Helper_Abstract
{
    /**
     * @param null $store
     * @return mixed
     */
    public function isRmaActive($store = null)
    {
        return Mage::getStoreConfigFlag('diglin_swisspost/rma/active', $store);
    }

    /**
     * @return string
     */
    public function getRmaMethod($store = null)
    {
        return Mage::getStoreConfig('diglin_swisspost/rma/method', $store);
    }
}