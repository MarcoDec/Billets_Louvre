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
use OC\CoreBundle\Entity\Mylog;

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
            $nb_billets=0;
            foreach ($commande_globale->getCommandes() as $commande ) {
                if ($commande->getQuantity()!=0) {
                    // Calcul du nombre total de champ visiteur nécessaires pour l'item
                    $total_visiteurs = $commande->getQuantity()*$commande->getTarif()->getNbBillets();
                    $nb_billets+=$total_visiteurs;
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
            $commande_globale->setNbBillets($nb_billets);  // On a recalculé le nombre total de billets au cas où
            $em->persist($commande_globale);
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

}
