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
 * Class Diglin_SwissPost_Model_Source_Shipping_Code
 */
class Diglin_SwissPost_Model_Source_Shipping_Code
{

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
          '98'  => '98',
          '99'  => '99',
        );
    }

}