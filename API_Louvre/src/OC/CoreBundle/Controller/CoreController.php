<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OC\CoreBundle\Entity\InitCommande;
use OC\CoreBundle\Form\InitCommandeType;
use OC\CommandeBundle\Entity\CommandeGlobale;
use OC\CommandeBundle\Entity\CommandeTarif;
use OC\CommandeBundle\Form\CommandeGlobaleType;
use OC\CommandeBundle\Form\CommandeGlobale2Type;
use OC\CommandeBundle\Form\CommandeTarifType;
use OC\CommandeBundle\Form\TarifType;
use OC\CommandeBundle\Entity\Tarif;
use OC\CoreBundle\Entity\User;

class CoreController extends Controller
{
    private function recoverSession($sessionId) {
        $commandesGlobalesSession =  $this->getDoctrine()->getManager()
                ->getRepository('OCCommandeBundle:CommandeGlobale')
                ->findBySessionId($sessionId);
        if (count($commandesGlobalesSession)>0) { // Si le tableau retourné contient au moins 1 élément, on récupère le 1er seulement
                return $commandesGlobalesSession[0];
        } else {
            $commande = new CommandeGlobale();
            $commande->setSessionId($sessionId);
            // On initialise les commandes
            $tarifsRepository= $this->getDoctrine()->getManager()->getRepository('OCCommandeBundle:Tarif');
            $list_tarifs=$tarifsRepository->findAll();
            foreach($list_tarifs as $tarif) {
                $commande_tarif=new CommandeTarif();
                $commande_tarif->setCommandeGlobale($commande);
                $commande_tarif->setTarif($tarif);
                $commande_tarif->setQuantity(0);
                $commande->addCommande($commande_tarif);
            }
            return $commande;
        }
    }
    
    /**
     * @Route("/", name="etape_1")
     */
    public function initialisationAction(Request $request)
    {
        //$logger = $this->get('logger');
        $commandeGlobale_session = new CommandeGlobale();
        $commandeGlobale_session= $this->recoverSession(session_id());
        
        $form2=$this->get('form.factory')->create(new CommandeGlobaleType(), $commandeGlobale_session);
        
        if ($request->isMethod('POST')) {
            $form2->handleRequest($request); // Récupération des données de la requête
           //print_r($request);
            if ($form2->isValid()) { 
                $em=$this->getDoctrine()->getManager();
                $em->persist($commandeGlobale_session);
                $em->flush();
                
                $url = $this->get('router')->generate( 'etape_2', array(
                    'id' => $commandeGlobale_session->getId())
                              );
                return new RedirectResponse($url); 
            }
        }
        $tarifsRepository= $this->getDoctrine()->getManager()->getRepository('OCCommandeBundle:Tarif');
        $list_tarifs=$tarifsRepository->findAll();
        return $this->render('OCCoreBundle:Default:initialisation.html.twig', 
                             array(
                                 'tarifs'=>$list_tarifs,
                                 'form2'=>$form2->createView(),
                                 'commandeGlobale'=>$commandeGlobale_session
                             )
                            );
    }
    
    /**
    * @Route("/Commande/Step2/{id}", name="etape_2")
    */
    public function data_clientAction(Request $request, $id ) {
        $CommandeGlobale = new CommandeGlobale();
        $em = $this->getDoctrine()->getManager(); 
        $CommandeGlobaleRepository = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $commandeGlobale=$CommandeGlobaleRepository->find($id);
        
        // On vérifie que l'ID de session de la commande correspond bien à l'Id de session de la personne
        if ($request->getSession()->getId()!=$commandeGlobale->getSessionId()) {
            throw $this->createNotFoundException("La page que vous demandez est indisponible. >(");
        }
        foreach ($commandeGlobale->getCommandes() as $commande ) {
            $nbIteration = $commande->getQuantity()*$commande->getTarif()->getNbBillets();
            for ($i=0; $i<$nbIteration; $i++) {
                $commande->addVisiteur(new User());
            }
        }
        // TODO: Créer sous-objets commandesDetaillee pour etape_2
        $form=$this->get('form.factory')->create(new CommandeGlobale2Type(), $commandeGlobale);
        
        return $this->render('OCCoreBundle:Default:dataClient.html.twig', array(
             'initCommande'=>$commandeGlobale,
             'form'=>$form->createView()));
    }
}
