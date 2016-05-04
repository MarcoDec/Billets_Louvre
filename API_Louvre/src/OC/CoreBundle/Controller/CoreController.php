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
    /**
    * Cette fonction complète les items manquant de la commande si besoin
    * et ajoute ou supprime autant de champs visiteurs que nécessaire
    */
    private function completeCommandes(&$commande_globale, &$em, &$error=false) {
        // Si la commande ne contient aucun item, alors on les initialise
        if (count($commande_globale->getCommandes())==0) {
            $commande_globale->initCommandes($this);
            $commande_globale->setNbBillets(0);
            $error=true; // Parce qu'à ce stade, il n'est pas normal que l'objet n'ait pas d'items.
        } else {
            // On parcours l'ensemble des items pour lui ajouter ou supprimer les champs visiteur utiles
            foreach ($commande_globale->getCommandes() as $commande ) {
                if ($commande->getQuantity()!=0) {
                    // Calcul du nombre total de champ visiteur nécessaires pour l'item
                    $total_visiteurs = $commande->getQuantity()*$commande->getTarif()->getNbBillets();

                    if (count($commande->getVisiteurs())==$total_visiteurs) {
                        // On ne fait rien car tous les visiteurs ont déjà été initialisés
                    } else if (count($commande->getVisiteurs()) < $total_visiteurs) {
                        // On ajoute les champs visiteur manquants
                        $nb_add=$total_visiteurs - count($commande->getVisiteurs());
                        for ($i=0; $i<$nb_add; $i++) {
                            $newUser = new User();
                            $em->persist($newUser); // on le persiste
                            $commande->addVisiteur($newUser); // on l'ajoute à la commande
                        }
                        
                    } else if (count($commande->getVisiteurs()) > $total_visiteurs) {
                        // On supprime les champs visiteur en trop en partant de la fin
                        $nb_supp=count($commande->getVisiteurs()) - $total_visiteurs; // nombre de champ à supprimer
                        $nb_visiteur=count($commande->getVisiteurs())-1; // Indice de fin
                        for ($i=0; $i<$nb_supp; $i++) {
                            $userToRemove=$commande->getVisiteurs()[$nb_visiteur-$i];
                            $commande->removeVisiteur($userToRemove);
                            $em->persist($userToRemove);
                        }

                    } else {
                        // On initie complètement la liste (c'est la 1ère fois qu'on arrive ici)
                        for ($i=0; $i<$total_visiteurs; $i++) {
                            $newUser = new User();
                            $em->persist($newUser);
                            $commande->addVisiteur($newUser);
                        }
                    }
                    $em->persist($commande); // on persiste la commande
                }

            }
        }
        $em->flush(); // on  enregistre l'ensemble des modifications
    }

    /**
    * Cette fonction permet de récupérer l'ensemble des données de commande déjà renseigné par l'utilisateur
    * connecté en fonction de son identifiant de session 
    */
    private function recoverSession($sessionId, &$mess) {
        // On récupère ici l'ensemble des commandes que l'utilisateur a déjà passé sous sa session courante
        $commandes_globales_session =  $this->getDoctrine()->getManager()
                ->getRepository('OCCommandeBundle:CommandeGlobale')
                ->findBySessionId($sessionId);

        if (count($commandes_globales_session)>0 && !$commandes_globales_session[0]->getPaid()) {
            // Si la dernière commande n'est pas encore payée, on la récupère pour l'afficher dans le formulaire (si pas d'erreur)
            $mess="Récupération de la commande n° ".$commandes_globales_session[0]->getId();
            $em=$this->getDoctrine()->getManager();
            $this->completeCommandes($commandes_globales_session[0], $em, $error);
            if (!$error) {return $commandes_globales_session[0];} // Si pas d'erreur on retourne la commande pour finalisation, sinon on continue et on créée une nouvelle commande vierge
        }
        $commande = new CommandeGlobale();
        $commande->initCommandes($this);
        $commande->setSessionId($sessionId);
        $mess="Initialisation d'une nouvelle commande";
        return $commande;
    }


    /**
     * @Route("/", name="etape_1")
     */
    public function initialisationAction(Request $request)
    {
        $message=null; // Information utilisable pour debogguer si besoin
        $errors=null; // tableau des erreurs de formulaire envoyé indépendamment pour l'affichage twig.
        $commande_globale = new CommandeGlobale();
        
        // On récupère les données de sessions si elles existent
        $commande_globale = $this->recoverSession($request->getSession()->getId(),$message);

        $form2=$this->get('form.factory')->create(new CommandeGlobaleType(), $commande_globale);
        
        if ($request->isMethod('POST')) {
            $form2->handleRequest($request); // Récupération des données de la requête

            if ($form2->isValid()) { 
                $em=$this->getDoctrine()->getManager();
                $em->persist($commande_globale);    // on persiste immédiatement les données du formulaire
                $em->flush();   // On les enregistre 
                $this->completeCommandes($commande_globale, $em); // On adapte les champs visiteurs si besoin
                
                $url = $this->get('router')->generate( 'etape_2', array(
                    'id' => $commande_globale->getId())
                              );
                return new RedirectResponse($url); 
            } else {
                // Si le formulaire a retourné des erreurs on mémorise le tableau dans $error
                $errors=$form2->getErrors();
            }

        }
        // Lorsqu'on arrive ici, c'est soit la 1ère fois (via un GET), soit via un Postage de formulaire qui aurait échoué (avec contournement JS...)
        // Dans ces cas, on réaffiche simplement le formulaire.
        $repo= $this->getDoctrine()->getManager()->getRepository('OCCommandeBundle:Tarif');
        $tarifs=$repo->findAll();
        return $this->render('OCCoreBundle:Default:initialisation.html.twig', 
                             array(
                                 'tarifs'=>$tarifs,
                                 'form2'=>$form2->createView(),
                                 'commandeGlobale'=>$commande_globale,
                                 'formErrors' => $errors,
                             )
                            );
    }
    
    /**
    * @Route("/Commande/Details/{id}", name="etape_2")
    */
    public function data_clientAction(Request $request, $id ) {
        $commande_globale = new CommandeGlobale();
        $em = $this->getDoctrine()->getManager(); 
        $repo = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $commande_globale=$repo->find($id);
        
        // On vérifie que la commande n'est pas déjà payée (auquel cas on n'affiche pas le formulaire mais on renvoie à la 1ère étape
        if ($commande_globale->getPaid()) {
            $url = $this->get('router')->generate( 'etape_1', array());
            return new RedirectResponse($url);
            break;
        } 
        
        // On vérifie que l'ID de session de la commande correspond bien à l'Id de session de la personne
        if ($request->getSession()->getId()!=$commande_globale->getSessionId()) {
            throw $this->createNotFoundException("La page que vous demandez est indisponible. >(");
        }

        $formErrors=null;
        $form=$this->get('form.factory')->create(new CommandeGlobale2Type(), $commande_globale);

        if ($request->isMethod('POST')) {
            // Récupération des données de la requête
            
            $form->handleRequest($request); 

            if ($form->isValid()) { 
                $em=$this->getDoctrine()->getManager();
                $em->persist($commande_globale);
                // On récupère les données client
                $em->persist($commande_globale->getClient());
                // On récupère les données visiteurs
                foreach ($commande_globale->getCommandes() as $commande ) {
                    foreach ($commande->getVisiteurs() as $visiteur ) {
                        $em->persist($visiteur);
                    }
                    $em->persist($commande);
                }
                $em->flush();
                
                $url = $this->get('router')->generate( 'modes_payment', array('id' => $commande_globale->getId()));
                return new RedirectResponse($url);
            } else {
                $formErrors=$form->getErrors();
            }
        }

        return $this->render('OCCoreBundle:Default:dataClient.html.twig', array(
             'initCommande'=>$commande_globale,
             'form'=>$form->createView(),
             'formErrors'=> $formErrors
             )
            );
    }


    /**
    * @Route("/Commande/Paiement/{id}", name="modes_payment")
    */
    public function paiementAction(Request $request, $id)  {
        $commande_globale = new CommandeGlobale();
        $em = $this->getDoctrine()->getManager(); 
        $repo = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $commande_globale=$repo->find($id);
        $token="no token";
        $err="";
        $succ="";

        // On vérifie que l'ID de session de la commande correspond bien à l'Id de session de la personne
        if ($request->getSession()->getId()!=$commande_globale->getSessionId()) {
            throw $this->createNotFoundException("La page que vous demandez est indisponible. >(");
        }

        if ($commande_globale->isPaid()) {
            $succ="Cette commande a déjà été traitée.<br> Les billets vous ont été envoyés par email.";
        } elseif ($commande_globale->getAmount()==0) {
            // Il n'y a rien à payer (exemple que des billets Gratuit)
            $commande_globale->setStripe(false);
            $commande_globale->setPaid(true);
            $commande_globale->setDateCommande(new \Datetime());
            $em->persist($commande_globale);
            $em->flush();
            $succ="La commande vient d'être traitée.<br>Les billets ont bien été envoyés sur votre email.<br>Merci pour votre confiance, et à bientôt !";
            // Dans ce cas, on créé les billets et on les envoie par mail.
            // Et pour finir, on remercie le client
        } elseif ($request->isMethod('POST')) { 
            /************************************************
            * Traitement d'un paiement par Stripe
            *************************************************/
            \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secret_key'));
            $token=$request->request->get('stripeToken');

            try {
              $charge = \Stripe\Charge::create(array(
                "amount" => $commande_globale->getAmount(), // amount in cents, again
                "currency" => "eur",
                "source" => $token,
                "description" => $commande_globale->getDesc(),
                ));
              $commande_globale->setStripe(true);
              $commande_globale->setPaid(true);
              $commande_globale->setDateCommande(new \Datetime());
              $succ="Billets envoyés. Merci de votre confiance";
              // Ici on créera les billets et on les enverra par mail
              // Ensuite on renverra vers la page de remerciement
            } catch(\Stripe\Error\Card $e) {
                // Ici on renvoie vers une erreur sur la page de paiement avec le détail de l'erreur
                $err="L'erreur suivante est arrivée. Le paiement n'a pas pu être effectué.<br>".$e->__toString();
                $commande_globale->setStripe(true);
                $commande_globale->setPaid(false);
            }

            // Ici on mémorise dans la base l'état de la commande a bien été payé, puis on construit si besoin les billets PDF pour envoi par mail ensuite.
            $em->persist($commande_globale);
            $em->flush();
        }
        return $this->render('OCCoreBundle:Default:paymentChoice.html.twig', 
         array(
             'id'=>$id,
             'commande' => $commande_globale,
             'spy' => $token,
             'err' => $err,
             "succ" => $succ,

         )
        ); 
        }


}
