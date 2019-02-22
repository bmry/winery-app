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
        $waiterOrderMessage = json_decode($msg->body, true);

        $logMessage = [
            'action' =>'sommelier_received_order',
            'body' => [
                'message'=>$waiterOrderMessage
            ]
        ];
        $this->orderLogger->log($logMessage);

        $this->processOrder($waiterOrderMessage);
        $this->sendProcessedOrder();
    }

    private function processOrder($request)
    {
        $logMessage = ['action' =>'sommelier_start_order_processing', 'body' => ['message'=>$request['order_id']]];
        $this->orderLogger->log($logMessage);

        $wineNames = $request['items'];
        $wineAvailabilityStatus = [];
        foreach ($wineNames as $wineName){
            if(!$this->wineAvailable($wineName)){
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

        $this->response = json_encode(array('order_id' =>$request['order_id'], 'wine_status' => $wineAvailabilityStatus));
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
        $this->responseSender->addProcessedOrderToQueue($this->response);

        $logMessage = [
            'action' =>'sommelier_add_response_to_queue',
            'body' => [
                'order_id'=>$this->response['order_id'],
                'message'=>$this->response
            ]
        ];
        $this->orderLogger->log($logMessage);
    }

}