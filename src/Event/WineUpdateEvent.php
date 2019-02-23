<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/23/2019
 * Time: 10:07 PM
 */

namespace App\Event;


use App\Entity\Wine;

class WineUpdateEvent
{
    const name = 'app.wine.update';
    private $wine;

    public function __construct(Wine $wine)
    {
        $this->wine = $wine;
    }

    public function getWine()
    {
        return $this->wine;
    }

    public function getEntityManager(){
        return $this->getEntityManager();
    }
}