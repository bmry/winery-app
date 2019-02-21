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
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class WaiterService
{
    private  $entityManager;
    private  $rpcClient;

    public function __construct(EntityManagerInterface $entityManager, RpcClient $rpcClient)
    {
        $this->entityManager = $entityManager;
        $this->rpcClient = $rpcClient;
    }

    public function processCustomerOrder(Order $order){
        $somellierResponse = $this->sendCustomerOrderToSomellier($order);

        $this->sendSomellierResponseToCustomer($somellierResponse);
    }

    private function sendSomellierResponseToCustomer($somellierResponse) {
        $responses = $somellierResponse;

        return $responses;
    }

    private function sendCustomerOrderToSomellier(Order $order){

        $message = [
            'order_id' => $order->getId(),
            'items' => $this->getOrderItemsName($order->getOrderItems())
        ];

        $wineNames = $message['items'];
        $this->rpcClient->addRequest(json_encode($message),'sommellier_service', $order->getId());
        try {
            return $this->rpcClient->getReplies();
        } catch (AMQPTimeoutException $e) {
            throw new OrderCreateException($e->getMessage());
        }


    }

    private function getOrderItemsName($orderItems){
        $items = [];
        foreach ($orderItems as $item) {
            $items[] = $item->getWine()->getTitle();
        }
        return $items;
    }

}