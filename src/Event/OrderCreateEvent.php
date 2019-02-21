<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/20/2019
 * Time: 5:58 PM
 */

namespace App\Event;


use App\Entity\Order;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\Event;

class OrderCreateEvent extends Event
{
    const name = 'app.wine.orderCreate';
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getEntityManager(){
        return $this->getEntityManager();
    }
}