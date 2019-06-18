<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Model_Source_Config_SoapEndpoint
{

    const LIVE_MODE_ENDPOINT        = 'https://wsbc.post.ch/wsbc/barcode/v2_2';

    const DEVELOPMENT_MODE_ENDPOINT = 'https://int.wsbc.post.ch/wsbc/barcode/v2_2';

    public function getAllOptions()
    {
        $output = array();
        foreach ($this->toOptionArray() as $key => $value) {
            $output[] = array(
              'value' => $key,
              'label' => $value,
            );
        }
        return $output;
    }

    public function toOptionArray()
    {
        return array(
          self::LIVE_MODE_ENDPOINT          => 'Production Mode',
          self::DEVELOPMENT_MODE_ENDPOINT   => 'Development Mode',
        );
    }

}