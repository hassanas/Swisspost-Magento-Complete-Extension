<?php

class LevoSoft_SwissPost_Helper_Data extends Mage_Core_Helper_Abstract
{

    
    public function log($message, $level)
    {
       
            Mage::log($message, $level, 'swisspost.log');
        
    }

    public function trackingExists($orderId,$incrementId){
      
    }

    /**
     * Calculates total weight of a shipment.
     *
     * @param $shipment
     * @return int
     */
    public function calculateTotalShippingWeight($shipment)
    {
        
    }


    /**
     * @param $order object
     */
    public function calculateOrderQuantityToShip($order){
        
    }

}