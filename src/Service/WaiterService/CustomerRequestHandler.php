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
        $logMessage = [
            'action' =>'waiter_received_order',
            'body' => [
                'order_id'=>$order->getId()
            ]
        ];
        $this->orderLogger->log($logMessage);

        $message = [
            'order_id' => $order->getId(),
            'items' => $this->getOrderItemsName($order->getOrderItems())
        ];

        $this->producer->publish(json_encode($message),'order_request');

        $logMessage = [
            'action' =>'order_sent_to_somellier',
            'body' => [
                'message'=>$message
            ]
        ];
        $this->orderLogger->log($logMessage);
    }

    private function getOrderItemsName($orderItems){
        $items = [];
        foreach ($orderItems as $item) {
            $items[] = $item->getWine()->getTitle();
        }
        return $items;
    }


}