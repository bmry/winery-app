<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 12:36 AM
 */

namespace App\Service;


use App\Event\OrderProcessedEvent;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;


class WaiterResponseSenderService implements ConsumerInterface
{
    private  $entityManager;
    private  $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(AMQPMessage $msg)
    {
        $sommelierReponse = json_decode($msg->body, true);
        $this->processSommelierResponse($sommelierReponse);
        $this->notifyCustomer();
    }

    public function processSommelierResponse($response){
        $requestedWines = $response['items'];
        $orderId = $response['order'];
        $this->updateOrderStatus($orderId);
        $this->updateWineAvailabilityStatus($requestedWines);
        $this->notifyCustomer();

    }

    private function updateOrderStatus($orderId)
    {
        $order = $this->entityManager->getRepository('App\Entity\Order')->findOneBy(['id' => $orderId]);
        $order->setStatus('PROCESSED');
        $this->entityManager->persist($order);
    }

    private function updateWineAvailabilityStatus($wines)
    {
        $orderedItems = $wines;
        foreach($orderedItems as $item){
            $orderedItem = $this->entityManager->getRepository('App\Entity\OrderItem')->findOneBy(['name' => $item['wineName']]);
            $orderedItem->setAvailability($item['availabilityStatus']);
            $this->entityManager->persist($orderedItem);
        }

    }

    private function notifyCustomer(){

        $this->eventDispatcher->dispatch(OrderProcessedEvent::name, new OrderProcessedEvent());
    }


}