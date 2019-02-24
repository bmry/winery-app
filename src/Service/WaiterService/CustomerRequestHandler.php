<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service\WaiterService;


use App\Entity\Order;
use App\Log\OrderLogger;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class CustomerRequestHandler
{
    private  $entityManager;
    private  $producer;
    private $orderLogger;

    public function __construct(EntityManagerInterface $entityManager, ProducerInterface $producer, OrderLogger $orderLogger)
    {
        $this->entityManager = $entityManager;
        $this->producer = $producer;
        $this->orderLogger = $orderLogger;
    }

    public function sendCustomerOrderToSomellier(Order $order)
    {
        $this->orderLogger->logAction('waiter_received_order', $order->getId(), $order->getId());

        $message = [
            'order_id' => $order->getId(),
            'items' => $this->getOrderItemsName($order->getOrderItems())
        ];
        $this->producer->publish(json_encode($message),'order_request');

        $this->orderLogger->logAction('order_forwarded_to_somellier', $order->getId(), $message);
    }

    private function getOrderItemsName($orderItems){
        $items = [];
        foreach ($orderItems as $item) {

            $items[] = $item->getWine();
        }
        return $items;
    }



}