<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/20/2019
 * Time: 6:20 PM
 */

namespace App\Listener;


use App\Event\OrderCreateEvent;
use App\Service\WaiterService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderCreateListener implements EventSubscriberInterface
{
    private $waiterService;

    public function __construct(WaiterService $waiterService)
    {

        $this->waiterService = $waiterService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            OrderCreateEvent::name => 'onOrderCreate',
        );
    }

    public function onOrderCreate(OrderCreateEvent $orderCreateEvent){

        $order =  $orderCreateEvent->getOrder();
        $this->waiterService->processCustomerOrder($order);
    }
}