<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/22/2019
 * Time: 12:08 AM
 */

namespace App\Log;


use App\Entity\WineLog;
use Doctrine\ORM\EntityManagerInterface;


class WineUpdateLogger extends Logger
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    public  function persistLogRecordToDB($message) {

        $wineLog = new WineLog();
        $wineLog->setLogAction($message['action']);
        $wineLog->setMessage(json_encode($message['body']));
        $wineId = $message['body']['wine_id'];
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['id' => $wineId]);
        $wineLog->setWine($wine);
        $this->entityManager->persist($wineLog);
        $this->entityManager->flush($wineLog);
    }
}