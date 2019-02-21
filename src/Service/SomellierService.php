<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class SomellierService implements ConsumerInterface
{
    private  $entityManager;
    private  $logger;
    private  $response;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(AMQPMessage $msg)
    {
        $waiterOrderMessage = json_decode($msg->body, true);
        $this->processWaiterOrder($waiterOrderMessage);
        return $this->sendReponseToWaiter();
    }

    private function processWaiterOrder($request){
        $wineNames = $request['items'];
        $wineAvailabilityStatus = [];
        foreach ($wineNames as $wineName){
            if(!$this->wineAvailable($wineName)){
                $wineAvailabilityStatus[] = array('wineName' => $wineName, 'available' => false);
            }else{
                $wineAvailabilityStatus[] = array('wineName' => $wineName, 'available' => true);
            }
        }
        $this->response = json_encode($wineAvailabilityStatus);
    }

    private function wineAvailable($wineName){
        $available = true;
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['title' => $wineName]);
        if(!$wine){
            $available = false;
        }
        return $available;
    }

    public function sendReponseToWaiter(){
        return $this->response;
    }

}