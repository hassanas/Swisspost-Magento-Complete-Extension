<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */


class Diglin_SwissPost_Helper_Config_Label extends Mage_Core_Helper_Abstract
{
    /**
     * @return mixed
     */
    public function getLayout()
    {
        return Mage::getStoreConfig('diglin_swisspost/label/layout');
    }

    /**
     * @return mixed
     */
    public function getImageFileType()
    {
        return Mage::getStoreConfig('diglin_swisspost/label/image_filetype');
    }

    /**
     * @return mixed
     */
    public function getImageResolution()
    {
        return Mage::getStoreConfig('diglin_swisspost/label/image_resolution');
    }

    /**
     * @return mixed
     */
    public function getImageOutputFolder()
    {
        return Mage::getStoreConfig('diglin_swisspost/label/image_output_folder');
    }
}
