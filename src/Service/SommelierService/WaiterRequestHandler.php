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
        $this->logWaiterRequestHandling($waiterRequest);
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
            $wineName = $this->getWineNameById($wineId);
            if(!$this->wineAvailableOnOrderDate($wineName, $orderDate)){
                $wineAvailabilityStatus[] = array('wineName' => $wineName, 'availabilityStatus' => false);
            }else{
                $wineAvailabilityStatus[] = array('wineName' => $wineName, 'availabilityStatus' => true);
            }

            $logMessage = [
                'action' =>'sommelier_checking_wine_availability',
                'body' => [
                    'order_id'=>$request['order_id'],
                    'message'=>$wineAvailabilityStatus
                ]
            ];
            $this->orderLogger->log($logMessage);
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
        $logMessage = [
            'action' =>'sommelier_add_response_to_queue',
            'body' => [
                'order_id'=>$this->response['order_id'],
                'message'=>$this->response
            ]
        ];
        $this->orderLogger->log($logMessage);
    }

    private function getWineNameById($wineId){
        return $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['id' => $wineId])->getTitle();
    }

    private function logWaiterRequestHandling($waiterRequest){
        $logMessage = [
            'action' =>'sommelier_received_order',
            'body' => [
                'order_id' => $waiterRequest['order_id'],
                'message'=>$waiterRequest
            ]
        ];
        $this->orderLogger->log($logMessage);
    }
}