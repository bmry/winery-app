<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 4:02 PM
 */

namespace App\Listener;


use App\Event\OrderCreateEvent;
use App\Event\OrderProcessedEvent;
use App\Service\WaiterRequestHandlerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderProcessedListener implements EventSubscriberInterface
{
    private $waiterService;

    public function __construct(WaiterRequestHandlerService $waiterService)
    {
        $this->waiterService = $waiterService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            OrderProcessedEvent::name => 'onOrderProcessed',
        );
    }

    public function onOrderProcessed(OrderCreateEvent $orderCreateEvent)
    {

        $order = $orderCreateEvent->getOrder();
        $this->waiterService->processCustomerOrder($order);
    }
}