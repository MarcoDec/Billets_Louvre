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
        
        if ($request->isMethod('POST')) {

            $form=$this->get('form.factory')->create(new CommandeGlobale2Type(), $commandeGlobale);
            $form->handleRequest($request); // Récupération des données de la requête
            if ($form->isValid()) { 
                $em=$this->getDoctrine()->getManager();
                $em->persist($commandeGlobale);
                // On récupère les données client
                $em->persist($commandeGlobale->getClient());
                // On récupère les données visiteurs
                foreach ($commandeGlobale->getCommandes() as $commande ) {
                    foreach ($commande->getVisiteurs() as $visiteur ) {
                        $em->persist($visiteur);
                    }
                    $em->persist($commande);
                }
                $em->flush();
                
                $url = $this->get('router')->generate( 'modes_payment', array('id' => $commandeGlobale->getId()));
                return new RedirectResponse($url);
                
                /*$url = $this->get('router')->generate( 'paiement', array(
                    'id' => $commandeGlobale->getId())
                              );
                return new RedirectResponse($url);*/
            }
        } else { // GET
            foreach ($commandeGlobale->getCommandes() as $commande ) {
                $nbIteration = $commande->getQuantity()*$commande->getTarif()->getNbBillets();
                if (count($commande->getVisiteurs())==$nbIteration) {
                    // On ne fait rien car tout est déjà là
                } else if (count($commande->getVisiteurs()) < $nbIteration) {
                    // On complète les éléments manquants
                    $nb_add=$nbIteration - count($commande->getVisiteurs());
                    for ($i=0; $i<$nb_add; $i++) {
                        $newUser = new User();
                        $em->persist($newUser);
                        $commande->addVisiteur($newUser);
                    }
                } else if (count($commande->getVisiteurs()) > $nbIteration) {
                    // On supprime les éléments en trop
                    $nb_supp=count($commande->getVisiteurs()) - $nbIteration;
                    $nb_visiteur=count($commande->getVisiteurs())-1;
                    for ($i=0; $i<$nb_supp; $i++) {
                        $userToRemove=$commande->getVisiteurs()[$nb_visiteur-$i];
                        $commande->removeVisiteur($userToRemove);
                        $em->persist($userToRemove);
                    }
                } else {
                    // On initie complètement la liste
                    for ($i=0; $i<$nbIteration; $i++) {
                        $newUser = new User();
                        $em->persist($newUser);
                        $commande->addVisiteur($newUser);
                    }
                }
                $em->persist($commande);
            }
            
            $em->persist($commandeGlobale);
            $em->flush();
            $form=$this->get('form.factory')->create(new CommandeGlobale2Type(), $commandeGlobale);
        }
        return $this->render('OCCoreBundle:Default:dataClient.html.twig', array(
             'initCommande'=>$commandeGlobale,
             'form'=>$form->createView())
            );
    }


    /**
    * @Route("/Paiement/{id}", name="modes_payment")
    */
    public function paiementAction(Request $request, $id)  {
        $CommandeGlobale = new CommandeGlobale();
        $em = $this->getDoctrine()->getManager(); 
        $CommandeGlobaleRepository = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $commandeGlobale=$CommandeGlobaleRepository->find($id);
        $token="tuto va bene";
        $err="";
        $succ="";

        // On vérifie que l'ID de session de la commande correspond bien à l'Id de session de la personne
        if ($request->getSession()->getId()!=$commandeGlobale->getSessionId()) {
            throw $this->createNotFoundException("La page que vous demandez est indisponible. >(");
        }

        if ($request->isMethod('POST')) { 

            \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secret_key'));
            $token=$request->request->get('stripeToken');
            try {
              $charge = \Stripe\Charge::create(array(
                "amount" => $commandeGlobale->getAmount(), // amount in cents, again
                "currency" => "eur",
                "source" => $token,
                "description" => $commandeGlobale->getDesc(),
                ));
              $commandeGlobale->setStripe(true);
              $commandeGlobale->setPaid(true);
              $succ="Réussite";
            } catch(\Stripe\Error\Card $e) {
                // Ici on renvoie vers une erreur sur la page de paiement avec le détail de l'erreur
                $err="L'erreur suivante est arrivée. Le paiement n'a pas pu être effectué.<br>".$e->__toString();
                $commandeGlobale->setStripe(true);
                $commandeGlobale->setPaid(false);
            }
            // Ici on mémorise dans la base l'état de la commande a bien été payé, puis on construit si besoin les billets PDF pour envoi par mail ensuite.
            $em->persist($commandeGlobale);
            $em->flush();
            

        } 
                    return $this->render('OCCoreBundle:Default:paymentChoice.html.twig', 
                             array(
                                 'id'=>$id,
                                 'commande' => $commandeGlobale,
                                 'spy' => $token,
                                 'err' => $err,
                                 "succ" => $succ,

                             )
                            );
        }


}
