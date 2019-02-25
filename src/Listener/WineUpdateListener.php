<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 4:02 PM
 */

namespace App\Listener;

use App\Entity\Wine;
use App\Log\WineUpdateLogger;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WineUpdateListener implements EventSubscriberInterface
{
    private  $producer;
    private  $wineUpdateLogger;

    public function __construct(ProducerInterface $producer, WineUpdateLogger $wineUpdateLogger)
    {
        $this->producer = $producer;
        $this->wineUpdateLogger = $wineUpdateLogger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::postUpdate,
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($object);
        if($object instanceof Wine) {

            $wine = $object;
            if ($this->wineAvailableDateUpdated($changes)) {
                $this->notifyWaiterIfWineIsAvailableToday($wine);
                $this->logWineAvailableDateChange($wine, $changes);
            }
        }
    }

    private function wineAvailableDateUpdated($changes)
    {
        $dateIsChanged = false;

        if(isset($changes['publishDate'])){
            $dateIsChanged = true;
        }

        return $dateIsChanged;
    }

    private function addAvailableWineInfoToQueueForWaiter(Wine $wine){
        $message = [
            'wine_id' => $wine->getId(),
            'day_of_update' => $wine->getPublishDate()
        ];
        $this->wineUpdateLogger->logAction('WINE_INFO_ADDED_TO_QUEUE',$wine->getId(), $message);
        $this->producer->publish(json_encode($message),'wine_update');
    }

    private function wineAvailableDateIsToday(Wine $wine){
        $isToday = false;
        $today = new \DateTime();
        $today = $today->format('Y-m-d');
        $wineAvailableDate = $wine->getPublishDate();
        if($today == $wineAvailableDate->format('Y-m-d')){
            $isToday = true;
        }

        return $isToday;

    }

    private function notifyWaiterIfWineIsAvailableToday(Wine $wine){

        if ($this->wineAvailableDateIsToday($wine)){
            $this->addAvailableWineInfoToQueueForWaiter($wine);
        }
    }

    private function logWineAvailableDateChange($wine, $changes){
        $message = ['changes' => $changes];
        if ($this->wineAvailableDateIsToday($wine)) {
            $message['old_publish_date'] = $changes['publishDate'][0];
        }
        $this->wineUpdateLogger->logAction('DATE_UPDATE', $wine->getId(), $message);
    }
}