<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/22/2019
 * Time: 12:08 AM
 */

namespace App\Log;


use App\Entity\OrderLog;
use Doctrine\ORM\EntityManagerInterface;


class OrderLogger extends Logger
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    public function persistLogRecordToDB($message) {

        $orderLog = new OrderLog();
        $orderLog->setLogAction($message['action']);
        $orderLog->setMessage(json_encode($message['body']));
        $orderId = $message['body']['order_id'];
        $order = $this->entityManager->getRepository('App\Entity\Order')->findOneBy(['id' => $orderId]);
        $orderLog->setOrderId($order);
        $this->entityManager->persist($orderLog);
        $this->entityManager->flush($orderLog);
    }



}