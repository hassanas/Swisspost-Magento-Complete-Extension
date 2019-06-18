<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_SwissPost_Model_Recipient
 */
class Diglin_SwissPost_Model_Recipient
{
    protected $_name1;
    protected $_name2;
    protected $_street1;
    protected $_street2;
    protected $_zip;
    protected $_city;
    protected $_country = 'CH';

    /**
     * @return mixed
     */
    public function getName1()
    {
        return $this->_name1;
    }

    /**
     * @param mixed $name1
     * @return $this
     */
    public function setName1($name1)
    {
        $this->_name1 = $name1;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName2()
    {
        return $this->_name2;
    }

    /**
     * @param mixed $name2
     * @return $this
     */
    public function setName2($name2)
    {
        $this->_name2 = $name2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreet1()
    {
        return $this->_street1;
    }

    /**
     * @param mixed $street1
     * @return $this
     */
    public function setStreet1($street1)
    {
        $this->_street1 = $street1;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreet2()
    {
        return $this->_street2;
    }

    /**
     * @param mixed $street2
     * @return $this
     */
    public function setStreet2($street2)
    {
        $this->_street2 = $street2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->_zip;
    }

    /**
     * @param mixed $zip
     * @return $this
     */
    public function setZip($zip)
    {
        $this->_zip = $zip;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * @param mixed $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->_city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->_country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->_country = $country;

        return $this;
    }
}