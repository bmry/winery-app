<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/25/2019
 * Time: 8:21 PM
 */

namespace App\Tests\Functional;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit;

class OrderControllerTest extends WebTestCase
{


    public function testOrderListPage(){
        $client = static::createClient();

        $client->request('GET', '/waiter');


        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}