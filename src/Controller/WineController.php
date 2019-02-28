<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/23/2019
 * Time: 6:10 PM
 */

namespace App\Controller;


use App\Entity\Wine;
use App\Form\Type\WineType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WineController  extends AbstractController
{

    /**
     * @Route("/wine/edit/{id}", name="edit_wine")
     */
    public function editAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        if(null == $id){
            $wine = new Wine();
        }else{
            $wine = $em->getRepository('App\Entity\Wine')->findOneBy(['id' => $id]);
        }
        $form = $this->createForm(WineType::class, $wine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($wine);
            $em->flush();
            $this->addFlash('success', 'Wine Updated');
            return $this->redirect($this->generateUrl('wine_list'));
        }

        return $this->loadEditPage($form, $wine);
    }

    protected function loadEditPage($form, Wine $wine){

        return $this->render('Wine/edit.html.twig', array('form' => $form->createView(), 'object' =>$wine,));
    }

    /**
     * @Route("sommelier", name="wine_list")
     */
    public function listAction(){

        $wines = $this->getDoctrine()->getManager()->getRepository('App\Entity\Wine')->findAll();
        return $this->render('Wine/list.html.twig', ['wines' => $wines]);
    }

    /**
     * @Route("make_available/{id}", name="make_available")
     */
    public function makeWineAvailable($id){
        $em = $this->getDoctrine()->getManager();
        $wine = $em->getRepository('App\Entity\Wine')->findOneBy(['id' => $id]);
        $wine->setPublishDate(new \DateTime());
        $em->persist($wine);
        $em->flush();

        $this->addFlash('success', 'Wine Is Now Available For the Day');
        return $this->redirect($this->generateUrl('wine_list'));

    }

}