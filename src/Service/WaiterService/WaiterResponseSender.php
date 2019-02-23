<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service\WaiterService;


use App\Entity\Order;
use App\Entity\OrderItem;
use App\Event\OrderProcessedEvent;
use App\Log\OrderLogger;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class WaiterResponseSender implements ConsumerInterface
{
    private  $entityManager;
    private  $eventDispatcher;
    private $sommelierResponse;
    private $orderLogger;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, OrderLogger $logger)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderLogger = $logger;
    }

    public function execute(AMQPMessage $msg)
    {
        $this->sommelierResponse =  json_decode($msg->body, true);

        $orderUpdate = $this->updatedOrderBasedOnSommelierResponse($this->sommelierResponse);
        $this->updateOrderItemAvailability($orderUpdate);
        $this->broadcastOrderUpdate($orderUpdate);
    }

    public function updatedOrderBasedOnSommelierResponse($sommelierResponse){
        $orderId = $sommelierResponse['order_id'];
        $order = $this->entityManager->getRepository('App\Entity\Order')->findOneBy(['id' => $orderId]);
        $this->updateOrderStatus($order);
        $processedOrder = $order;
        $this->entityManager->flush($order);
        return $processedOrder;
    }

    private function updateOrderStatus(Order $order)
    {
        $order->setStatus('PROCESSED');
        $this->entityManager->persist($order);

    }

    private function updateOrderItemAvailability($order)
    {
        $orderedItems = $this->sommelierResponse['wine_status'];

        foreach($orderedItems as $item){
            $wineName =  $item['wineName'];
            $wineAvailabilityStatus = $item['availabilityStatus'];
            $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['title' => $wineName]);
            $orderedItem = $this->entityManager->getRepository('App\Entity\OrderItem')->findOneBy(['wine' => $wine,'orderId' => $order]);
            $orderedItem->setAvailable($wineAvailabilityStatus);
            $this->entityManager->persist($orderedItem);
            dump($orderedItem);
            $this->entityManager->flush();
        }
    }

    private function broadcastOrderUpdate(Order $order)
    {
        $this->eventDispatcher->dispatch(OrderProcessedEvent::name, new OrderProcessedEvent($order));
    }


}