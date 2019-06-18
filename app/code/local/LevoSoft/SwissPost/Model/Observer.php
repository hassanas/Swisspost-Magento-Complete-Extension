<?php

/**
 * User: hassan
 * Date: 22-Mar-17
 * Time: 2:01 AM
 */
class LevoSoft_SwissPost_Model_Observer
{

    public function sendTrackingEmailToCustomer($observer)
    {
        $track = $observer->getEvent()->getTrack();
        $shipment = $track->getShipment(true);
        $shipment->sendEmail();

    }
}