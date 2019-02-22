<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/20/2019
 * Time: 6:20 PM
 */

namespace App\Listener;


use App\Event\OrderCreateEvent;
use App\Service\WaiterService\CustomerRequestHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderCreateListener implements EventSubscriberInterface
{
    private $waiterRequestSender;
    private $producer;

    public function __construct(CustomerRequestHandler $waiterRequestSender)
    {
        $this->waiterRequestSender = $waiterRequestSender;
    }

    public static function getSubscribedEvents()
    {
        return array(
            OrderCreateEvent::name => 'onOrderCreate',
        );
    }

    public function onOrderCreate(OrderCreateEvent $orderCreateEvent){
        $order =  $orderCreateEvent->getOrder();

        $this->waiterRequestSender->sendCustomerOrderToSomellier($order);
    }

}