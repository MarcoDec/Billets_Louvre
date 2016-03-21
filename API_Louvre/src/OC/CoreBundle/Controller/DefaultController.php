<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OC\CoreBundle\Entity\InitCommande;
use OC\CoreBundle\Form\InitCommandeType;
use OC\CommandeBundle\Entity\CommandeGlobale;
use OC\CommandeBundle\Form\CommandeGlobaleType;
use OC\CommandeBundle\Entity\Tarif;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="etape_1")
     */
    public function initialisationAction(Request $request)
    {
        $initCommande= new CommandeGlobale();
        $form=$this->get('form.factory')->create(new CommandeGlobaleType(), $initCommande);
        
        $initCommande->setSessionId($request->getSession()->getId());
        
        if ($form->handleRequest($request)->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($initCommande);
            $em->flush();
            
            $url = $this->get('router')->generate( 'etape_2', array(
                'id' => $initCommande->getId())
                              );
            return new RedirectResponse($url); 
        }
        return $this->render('OCCoreBundle:Default:initialisation.html.twig', 
                             array(
                                 'initCommande'=>$initCommande,
                                 'form'=>$form->createView(),
                                 'tarifs'=>Tarif::getTarifs()
                             )
                            );
    }
    
    /**
    * @Route("/Commande/Step1/{id}", name="etape_2")
    */
    public function data_clientAction(Request $request, $id ) {
        $em = $this->getDoctrine()->getManager(); 
        $initCommandeRepository = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $initCommande=$initCommandeRepository->find($id);
        //On vérifie que l'ID de session correspond bien à l'Id de session de la personne
        if ($request->getSession()->getId()!=$initCommande->getSessionId()) {
            throw $this->createNotFoundException("La page que vous demandez est indisponible pour vous.");
        }
        
         return $this->render('OCCoreBundle:Default:dataClient.html.twig', array('initCommande'=>$initCommande));
    }
}
