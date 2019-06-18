<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sascha Michalski <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_SwissPost
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

class Diglin_SwissPost_Helper_Util extends Mage_Core_Helper_Abstract
{

    public function getElements($root) {
        if ($root == null) {
            return array();
        }
        if (is_array($root)) {
            return $root;
        }
        else {
            return array($root);
        }
    }

    public function toCommaSeparatedString($strings) {
        $res        = '';
        $delimiter  = '';
        foreach ($strings as $str) {
            $res    .= $delimiter.$str;
            $delimiter = ', ';
        }
        return $res;
    }

}