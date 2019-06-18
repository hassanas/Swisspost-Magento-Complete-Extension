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
 * Class Diglin_SwissPost_Model_Source_Shipping_LabelLayout
 */
class Diglin_SwissPost_Model_Source_Shipping_LabelLayout
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
          'A6'  => 'A6',
          'A5'  => 'A5',
          'A7'  => 'A7',
        );
    }

}