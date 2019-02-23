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


class OrderLogger
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    public  function log($message) {
        $orderLog = new OrderLog();
        $orderLog->setLogAction($message['action']);
        $orderLog->setMessage(json_encode($message['body']));
        $this->entityManager->persist($orderLog);
        $this->entityManager->flush($orderLog);
    }
}