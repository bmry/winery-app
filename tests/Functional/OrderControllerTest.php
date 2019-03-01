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

class OrderControllerTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

    }

    public function testCreateOrder(){
        $orderPageContent = $this->client->request(
            'GET',
            $this->generateUrl('new_order')
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $form = $this->getOrderFormFromOrderPageContent($orderPageContent);
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $expectedMessage = "Your order has been received and its being processed. We will get back to you shortly via the email address you provided.";
        $this->assertContains($expectedMessage, $response->getContent());
    }

    public function testOrderListPage(){

        $orderListPageContent =  $this->client->request('GET', '/waiter');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Customer Order', $response->getContent());

        $this->assertTrue(
            $orderListPageContent->filter('table#orderList tr.orderRow')->count() > 0,
            'No order listed');


    }

    private function getOrderFormFromOrderPageContent ($ordePageContent){

        $token = $ordePageContent->filter('input#order__token')
            ->attr('value');
        $this->assertTrue(($token && null !== $token), 'token not found');

        if (!$token) {
            throw new \Exception('Unable to submit form without token');
        }
        $form = $ordePageContent->selectButton("{$this->trans('form.label.btn_save')}")->form();
        $formName = 'order';

        $wineOptionValue = $ordePageContent
            ->filter("#{$formName}_orderItems_0_wine option:contains(\"Wine Spectator\")")
            ->attr('value');

        $form["{$formName}[customerContactEmail]"] = 'bamgbosemorayo@gmail.com';
        $form["{$formName}[orderItems][0][wine]"]->select($wineOptionValue);

        return $form;
    }

}