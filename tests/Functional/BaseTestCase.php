<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 3/1/2019
 * Time: 2:53 AM
 */

namespace App\Tests\Functional;


use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BaseTestCase extends WebTestCase
{


    /** @var Client */
    public $client;

    /** @var Container */
    public $container;
    public $entityManager;


    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        self::bootKernel();
        $this->container = self::$kernel->getContainer();

        $this->entityManager = $this->container->get('doctrine')->getManager();
    }


    public function tearDown()
    {
        $this->entityManager->close();
        $doctrine = $this->container->get('doctrine');
        foreach ($doctrine->getConnections() as $connection) {
            $connection->close();
        }
        // Container
        $doctrine = null;
        $this->container = null;
        $this->entityManager = null;
        $this->businessInstance = null;

        parent::tearDown();
    }

    public function generateUrl($route, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $uri = $this->container->get('router')->generate($route, $parameters, $referenceType);
        $uri = str_ireplace('/app_dev.php', '', $uri);

        return $uri;
    }

}