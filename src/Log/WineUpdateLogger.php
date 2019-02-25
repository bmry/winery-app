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

        if(isset($message['body']['message']['old_publish_date'])){
            $wineLog->setOldPublishDate($message['body']['message']['old_publish_date']);
        }
        $wineId = $message['body']['id'];
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['id' => $wineId]);
        $wineLog->setWine($wine);
        $this->entityManager->persist($wineLog);
        $this->entityManager->flush($wineLog);
    }
}