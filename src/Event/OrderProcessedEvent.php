<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 4:02 PM
 */

namespace App\Event;


use Symfony\Component\EventDispatcher\Event;

class OrderProcessedEvent extends Event
{
    const name = 'app.wine.orderProcessed';

}