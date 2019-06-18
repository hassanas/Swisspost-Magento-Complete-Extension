<?php

class LevoSoft_SwissPost_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Logs bugs/info.
     * Zend_Log::DEBUG = 7
     * Zend_Log::ERR = 3
     * Zend_Log::INFO = 6
     *
     * @param $message
     * @param $level
     */
    public function log($message, $level)
    {
        //$allowedLogLevel = Mage::getStoreConfig('carriers/dpdparcelshops/log_level');
        //if ($level <= $allowedLogLevel) {
            Mage::log($message, $level, 'swisspost.log');
        //}
    }

    public function trackingExists($orderId,$incrementId){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT track_number FROM ' . $resource->getTableName('sales/shipment_track'). ' WHERE order_id ='.$orderId;
        $track_number = $readConnection->fetchOne($query);
        if($track_number!=false){
            $trackingCode   = Mage::helper('diglin_swisspost/tracking')->formatTrackingNumberToShippingNumber($track_number);
            return str_replace(Mage::getBaseDir(),"",Mage::helper('diglin_swisspost/label')->getLabelPath($incrementId, $trackingCode));
        }
        return $track_number;
    }

    /**
     * Calculates total weight of a shipment.
     *
     * @param $shipment
     * @return int
     */
    public function calculateTotalShippingWeight($shipment)
    {
        $weight = 0;
        $shipmentItems = $shipment->getAllItems();
        foreach ($shipmentItems as $shipmentItem) {
            $orderItem = $shipmentItem->getOrderItem();
            if(!$orderItem->getParentItemId()){
                $weight = $weight + ($shipmentItem->getWeight() * $shipmentItem->getQty());
            }
        }

        return $weight;
    }


    /**
     * @param $order object
     */
    public function calculateOrderQuantityToShip($order){
        foreach($order->getAllItems() as $eachOrderItem){

            $Itemqty=0;
            $Itemqty = $eachOrderItem->getQtyOrdered()
                - $eachOrderItem->getQtyShipped()
                - $eachOrderItem->getQtyRefunded()
                - $eachOrderItem->getQtyCanceled();
            $qty[$eachOrderItem->getId()]=$Itemqty;

        }
        return $qty;
    }

}