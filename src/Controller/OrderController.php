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
use App\Form\Type\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    /**
     * @Route("/order", name="new_order")
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
            $em->persist($order);
            $em->flush();
            $this->addFlash('success', 'Your order has been received and its being processed. We will get back to you shortly via the email address you provided.');
            $this->redirect($this->generateUrl('new_order'));
        }

        return $this->render('Order/new.html.twig', ['form' => $form->createView()]);
    }
}