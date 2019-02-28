<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/22/2019
 * Time: 11:44 AM
 */

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Event\OrderCreateEvent;
use App\Form\Type\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="new_order")
     */
    public function newAction(Request $request)
    {
        $order = new  Order();
        $orderItem = new OrderItem();
        $wine = $this->getDoctrine()->getManager()->getRepository('App\Entity\Wine')->findOneBy(['id' =>20]);
        $orderItem->setWine($wine);
        $order->addOrderItem($orderItem);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $order->setCreatedAt(new  \DateTime(date('Y-m-d')));
            $em->persist($order);
            $em->flush();
            $this->addFlash('success', 'Your order has been received and its being processed. We will get back to you shortly via the email address you provided.');
            $this->eventDispatcher->dispatch(OrderCreateEvent::name, new OrderCreateEvent($order));

            return $this->redirect($this->generateUrl('new_order'));
        }

        return $this->render('Order/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("waiter", name="order_list")
     */
    public function listAction(){

        $orders = $this->getDoctrine()->getManager()->getRepository('App\Entity\Order')->getAllOrders();
        return $this->render('Order/list.html.twig', ['orders' => $orders]);
    }

    /**
     * @Route("/order/get_order_items/{order_id}", name="get_order_items")
     */
    public function getOrderItemAction($order_id){
        $order = $this->getDoctrine()->getManager()->getRepository('App\Entity\Order')->findOneBy(['id' => $order_id]);
        return $this->render('OrderItem/partial_view.html.twig', ['orderItems' => $order->getOrderItems()]);
    }

    /**
     * @Route("/order/get_order_logs/{order_id}", name="get_order_logs")
     */
    public function getOrderLogAction($order_id){
        $order = $this->getDoctrine()->getManager()->getRepository('App\Entity\Order')->findOneBy(['id' => $order_id]);
        return $this->render('OrderLog/partial_view.html.twig', ['orderLogs' => $order->getOrderLogs()]);
    }
}