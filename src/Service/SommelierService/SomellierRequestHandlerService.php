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
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class SomellierRequestHandlerService implements ConsumerInterface
{
    private  $entityManager;
    private  $response;
    private  $responseSender;

    public function __construct(EntityManagerInterface $entityManager, SomellierResponseSenderService $responseSender )
    {
        $this->entityManager = $entityManager;
        $this->responseSender = $responseSender;
    }

    public function execute(AMQPMessage $msg)
    {
        $waiterOrderMessage = json_decode($msg->body, true);
        $this->processWaiterOrder($waiterOrderMessage);
        return $this->sendProcessedOrder();
    }

    private function processWaiterOrder($request)
    {
        $response = [];
        $wineNames = $request['items'];
        $response[] = ['order_id' => $request['order_id']];
        $wineAvailabilityStatus = [];
        foreach ($wineNames as $wineName){
            if(!$this->wineAvailable($wineName)){
                $wineAvailabilityStatus[] = array('wineName' => $wineName, 'availabilityStatus' => false);
            }else{
                $wineAvailabilityStatus[] = array('wineName' => $wineName, 'availabilityStatus' => true);
            }
        }
        $response[] = $wineAvailabilityStatus;
        $this->response = json_encode($response);
    }

    private function wineAvailable($wineName){
        $available = true;
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['title' => $wineName]);
        if(!$wine){
            $available = false;
        }
        return $available;
    }

    public function sendProcessedOrder(){
        $this->responseSender->getPrcocessedOrder($this->response);
    }

}