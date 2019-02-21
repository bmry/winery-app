<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;


class SomellierResponseSenderService
{
    private  $entityManager;
    private  $response;


    public function __construct(EntityManagerInterface $entityManager, ProducerInterface $producer)
    {
        $this->entityManager = $entityManager;
        $this->producer = $producer;
    }

    private function addProcessedOrderToOrderQueue($processedOrder){

        $this->producer->publish($this->response,'request');
    }

    public function getPrcocessedOrder($response){
        $this->response =  $response;
        return $this->response;
    }

}