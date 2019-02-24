<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service\SommelierService;


use App\Log\OrderLogger;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class WaiterRequestHandler implements ConsumerInterface
{
    private  $entityManager;
    private  $response;
    private  $responseSender;
    private  $orderLogger;

    public function __construct(EntityManagerInterface $entityManager, SommelierResponseHandler $responseSender, OrderLogger $orderLogger )
    {
        $this->entityManager = $entityManager;
        $this->responseSender = $responseSender;
        $this->orderLogger = $orderLogger;
    }

    public function execute(AMQPMessage $msg)
    {
        $waiterRequest = json_decode($msg->body, true);
        $this->processWaiterRequest($waiterRequest);
        $this->sendProcessedOrder();
        $this->orderLogger->logAction('sommelier_received_order',$waiterRequest['order_id'], $waiterRequest);
    }

    private function processWaiterRequest($request)
    {
        $logMessage = ['action' =>'sommelier_start_order_processing', 'body' => ['order_id'=>$request['order_id'],'message'=>$request]];
        $this->orderLogger->log($logMessage);
        $wineIds = $request['items'];
        $wineAvailabilityStatus = [];
        $order = $this->entityManager->getRepository('App\Entity\Order')->findOneBy(['id' => $request['order_id']]);
        $orderDate = $order->getCreatedAt();

        foreach ($wineIds as $wineId){
            $this->getWineAvailabilityStatusOnOrderDate($wineId, $orderDate);
            $this->orderLogger->logAction('sommelier_checking_wine_availability',$request['order_id'], $request);
        }

        $this->response = array('order_id' =>$request['order_id'], 'wine_status' => $wineAvailabilityStatus);
    }

    private function wineAvailableOnOrderDate($wineName, $orderDate){
        $available = true;
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->getWineByNameAndDate($wineName,$orderDate);
        if(!$wine){
            $available = false;
        }
        return $available;
    }

    public function sendProcessedOrder(){
        $this->responseSender->addProcessedOrderToQueue(json_encode($this->response));
        $this->orderLogger->logAction('sommelier_add_response_to_queue',$this->response['order_id'], $this->response);
    }

    private function getWineNameById($wineId){
        return $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['id' => $wineId])->getTitle();
    }


    private function getWineAvailabilityStatusOnOrderDate($wineId, $orderDate){
        $wineName = $this->getWineNameById($wineId);
        if(!$this->wineAvailableOnOrderDate($wineName, $orderDate)){
            $wineAvailabilityStatus[] = array('wineName' => $wineName, 'availabilityStatus' => false);
        }else{
            $wineAvailabilityStatus[] = array('wineName' => $wineName, 'availabilityStatus' => true);
        }
    }
}