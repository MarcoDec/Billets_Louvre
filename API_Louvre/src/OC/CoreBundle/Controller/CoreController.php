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
use OC\CommandeBundle\Form\CommandeTarifType;
use OC\CommandeBundle\Form\TarifType;
use OC\CommandeBundle\Entity\Tarif;

class CoreController extends Controller
{
    /**
     * @Route("/", name="etape_1")
     */
    public function initialisationAction(Request $request)
    {
            $logger = $this->get('logger');
            $logger->info('# Controlleur <initialisationAction>');
        // POST et GET : Initialisation de l'Entité manipulée dans ce formulaire
        $commandeGlobale_session = new CommandeGlobale();
        
        // POST et GET : Recherche dans la base si une commandeGlobale associée à la session courante a déjà été mémorisée, et si oui on la récupère.
        $commandesGlobalesSession =  $this->getDoctrine()->getManager()
            ->getRepository('OCCommandeBundle:CommandeGlobale')
            ->findBySessionId($request->getSession()->getId());
        
        if (count($commandesGlobalesSession)>0) { // Si le tableau retourné contient au moins 1 élément, on récupère le 1er seulement
            $commandeGlobale_session=$commandesGlobalesSession[0];
                $logger->info('Session déjà mémorisée => Id = '.$commandeGlobale_session->getSessionId());
        } else { // Si le tableau retourné est vide, la nouvelle session doit être mémorisée
            $commandeGlobale_session->setSessionId($request->getSession()->getId());
                $logger->info('Nouvelle Session => Id = '.$commandeGlobale_session->getSessionId());
        }
            $logger->info('$commandeGlobale_session : '.$commandeGlobale_session->getNbBillets().' billets');
        // POST et GET :  Dans tous les cas on complète si besoin la table des commandes pour en avoir une par tarif avec quantitée initialisée à 0
        $tarifsRepository= $this->getDoctrine()->getManager()->getRepository('OCCommandeBundle:Tarif');
        $list_tarifs=$tarifsRepository->findAll();
        $m='Chargement de la grille des Tarifs qui contiennent '.((string) count($list_tarifs)).' tarif(s)';
            $logger->info($m);
            $logger->info('Completion du tableau qui contient '.(count($commandeGlobale_session->getCommandes())).' commande(s)');
        if (count($list_tarifs)>count($commandeGlobale_session->getCommandes())) {
            foreach($list_tarifs as $tarif) {
                if ( $commandeGlobale_session->hasTarif($tarif)) {
                    // on ne fait rien
                } else {
                    // On ajoute la ligne vide
                    $commande_tarif=new CommandeTarif();
                    $commande_tarif->setCommandeGlobale($commandeGlobale_session);
                    $commande_tarif->setTarif($tarif);
                    $commande_tarif->setQuantity(0);
                    $commandeGlobale_session->addCommande($commande_tarif);
                }
            }
        }
            $logger->info('Le tableau qui contient maintenant '.(count($commandeGlobale_session->getCommandes())).' commande(s)');

        // On initialise le formulaire 
        $form=$this->get('form.factory')->create(new CommandeGlobaleType(), $commandeGlobale_session);
        $form2=$this->get('form.factory')->create(new CommandeGlobaleType(), $commandeGlobale_session);
        
        
        if ($request->isMethod('POST')) {
            $logger->info('C\'est une requête POST');
            //print_r($request);
            $form->handleRequest($request); // Récupération des données de la requête
            $logger->info('Le tableau qui contient maintenant '.(count($commandeGlobale_session->getCommandes())).' commande(s)');
            $valBillet = $request->request->get('oc_commandebundle_CommandeGlobale_nbBillets');
            $logger->info('Le nombre de billet dans la Form est de '.$valBillet);
            
            $em=$this->getDoctrine()->getManager();
            $nbBillets=0;
            
            foreach ($commandeGlobale_session->getCommandes() as $commande) {
                if ($commande->getQuantity()==0) {
                    // on ne persiste pas
                    $commandeGlobale_session->removeCommande($commande);
                    $em->remove($commande);
                } else {
                    $nbBillets+=
                        $commande->getQuantity()*
                        $commande->getTarif()->getNbBillets();
                    //$em->persist($commande);
                }

            }
            
        } else {
            $logger->info('C\'est une requête GET');
        }
        /*if ($form->isValid()) { 
            // On persist CommandeGlobale et commandes.

            $commandeGlobale_session->setNbBillets($nbBillets);
            
            $em->persist($commandeGlobale_session);
            $em->flush();
            
            $url = $this->get('router')->generate( 'etape_2', array(
                'id' => $commandeGlobale_session->getId())
                              );
            return new RedirectResponse($url); 
        }*/

        return $this->render('OCCoreBundle:Default:initialisation.html.twig', 
                             array(
                                 'form'=>$form->createView(),
                                 'tarifs'=>$list_tarifs,
                                 'commandeGlobale'=>$commandeGlobale_session,
                                 'form2'=>$form2->createView()
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
