<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service;


use App\Entity\Order;
use App\Exception\OrderCreateException;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class WaiterRequestHandlerService
{
    private  $entityManager;
    private  $producer;


    public function __construct(EntityManagerInterface $entityManager, ProducerInterface $producer)
    {
        $this->entityManager = $entityManager;
        $this->producer = $producer;
    }

    public function processCustomerOrder(Order $order){
        $this->sendCustomerOrderToSomellier($order);
    }

    private function sendCustomerOrderToSomellier(Order $order){

        $message = [
            'order_id' => $order->getId(),
            'items' => $this->getOrderItemsName($order->getOrderItems())
        ];

        $this->producer->publish(json_encode($message),'request');
    }

    private function getOrderItemsName($orderItems){
        $items = [];
        foreach ($orderItems as $item) {
            $items[] = $item->getWine()->getTitle();
        }
        return $items;
    }

}