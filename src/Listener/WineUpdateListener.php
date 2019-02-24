<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 4:02 PM
 */

namespace App\Listener;

use App\Entity\Wine;
use App\Event\WineUpdateEvent;
use App\Log\WineUpdateLogger;
use App\Service\SommelierService\SommelierResponseHandler;
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

        if ($object instanceof Wine) {
            if ($this->wineAvailableDateUpdated($changes)) {
                $this->addAvailableWineInfoToQueueForWaiter($object);
            }
        }
    }

    private function wineAvailableDateUpdated($changes)
    {
        $dateChanged = false;
        $dateChanges = $changes['publishDate'];
        $oldAvailableDate = $changes['publishDate'][0];
        $newAvailableDate = $changes['publishDate'][1];
        if (!$dateChanges) {
            return $dateChanged;
        } else {
            if ($newAvailableDate > $oldAvailableDate) {
                $dateChanged = true;
                return $dateChanged;
            }
        }

        return $dateChanged;
    }

    private function addAvailableWineInfoToQueueForWaiter(Wine $wine){
        $message = [
            'wine_id' => $wine->getId(),
            'day_of_update' => $wine->getPublishDate()
        ];

        $this->producer->publish(json_encode($message),'wine_update');

        $logMessage = [
            'action' =>'DATE_UPDATE',
            'body' => [
                'wine_id' => $wine->getId(),
                'message'=>$message
            ]
        ];
        $this->wineUpdateLogger->log($logMessage);
    }
}