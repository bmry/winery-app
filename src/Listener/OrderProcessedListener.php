<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 4:02 PM
 */

namespace App\Listener;

use App\Event\OrderProcessedEvent;
use App\Service\SommelierService\SommelierResponseHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderProcessedListener implements EventSubscriberInterface
{
    private $waiterService;

    public function __construct(SommelierResponseHandler $waiterService)
    {
        $this->waiterService = $waiterService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            OrderProcessedEvent::name => 'onOrderProcessed',
        );
    }

    public function onOrderProcessed(OrderProcessedEvent $orderProcessedEvent)
    {
        $order = $orderProcessedEvent->getOrder();
        $customerContactEmail = $order->getCustomer()->getEmail();
        $this->sendMailToCustomer($customerContactEmail);
        // Send Customer Email
        //Send Customer SMS
        dump($order);
    }

    private function sendMailToCustomer($customerEmail){

    }
}