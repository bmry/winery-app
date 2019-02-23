<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service\SommelierService;


use App\Entity\Order;
use App\Log\OrderLogger;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;


class SommelierResponseHandler
{
    private  $entityManager;
    private  $orderLogger;
    public function __construct(EntityManagerInterface $entityManager, ProducerInterface $producer, OrderLogger $orderLogger)
    {
        $this->entityManager = $entityManager;
        $this->producer = $producer;
        $this->orderLogger = $orderLogger;
    }


    public function addProcessedOrderToQueue($processedOrder){
        $message = json_decode($processedOrder);
        $orderId = $message->order_id;
        $logMessage = [
            'action' =>'sommelier_dropping_order_for_waiter',
            'body' => [
                'order_id' => $orderId,
                'message'=>$processedOrder
            ]
        ];

        $this->orderLogger->log($logMessage);
        $this->producer->publish($processedOrder,'order_response');

    }

}