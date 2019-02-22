<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 4:02 PM
 */

namespace App\Event;


use App\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

class OrderProcessedEvent extends Event
{
    const name = 'app.wine.orderProcessed';
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