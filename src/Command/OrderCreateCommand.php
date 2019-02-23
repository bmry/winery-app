<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/21/2019
 * Time: 2:13 AM
 */

namespace App\Command;


use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Event\OrderCreateEvent;
use App\Log\OrderLogger;
use App\Service\SommelierService\SommelierResponseHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderCreateCommand extends Command
{

    private $waiterService;
    private $entityManager;
    private $eventDispatcher;
    private $orderLogger;


    public function __construct(EntityManagerInterface $entityManager, SommelierResponseHandler $waiterService, EventDispatcherInterface $eventDispatcher, OrderLogger $orderLogger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->waiterService = $waiterService;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderLogger = $orderLogger;

    }

    protected function configure()
    {
        $this
            ->setName('app:order_create')
            ->setDescription('This helps extract the RSS Feed content into the databae');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Process started');
        $this->orderLogger->log(['action' =>'Order started', 'body' => ['time'=>date('Y-m-d H:i:s')]]);

        $order = $this->getTestOrder();
        $logMessage = ['action' =>'Order Created', 'body' => ['order_id'=>$order->getId()]];
        $this->orderLogger->log($logMessage);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(OrderCreateEvent::name, new OrderCreateEvent($order));
        $output->writeln('Process finished');
    }


    private function getTestOrder(){
        $order = new Order();
        $wine = $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['id' => 20]);
        $customer = new Customer();
        $customer->setName('Morayo');

        $orderItem = new OrderItem();
        $orderItem->setOrderId($order);
        $orderItem->setWine($wine);

        $order->addOrderItem($orderItem);
        $this->entityManager->persist($order);

        return $order;
    }
}