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
        $commandeGlobale= new CommandeGlobale();
        
        $tarifsRepository= $this->getDoctrine()->getManager()->getRepository('OCCommandeBundle:Tarif');
        $list_tarifs=$tarifsRepository->findAll();
        
        foreach($list_tarifs as $tarif) {
            $commande_tarif=new CommandeTarif();
            $commande_tarif->setCommandeGlobale($commandeGlobale);
            $commande_tarif->setTarif($tarif);
            $commande_tarif->setQuantity(0);
            $commandeGlobale->addCommande($commande_tarif);
        }
        $commandeGlobale->setSessionId($request->getSession()->getId());
        
        $form=$this->get('form.factory')->create(new CommandeGlobaleType(), $commandeGlobale);
        
        $tarife= new Tarif();
        $tarife->setDescription('Ceci est ma description');
        $form_tarif=$this->get('form.factory')->create(new TarifType(), $tarife);
        
        $commandeTarif = new CommandeTarif();
        //$commandeTarif->setTarif( new Tarif());
        $form_commandeTarif=$this->get('form.factory')->create(new CommandeTarifType(), $commandeTarif);
        
        if ($form->handleRequest($request)->isValid()) {
            $tarifs=Tarif::getTarifs();
            $tarifs_commandes= array();
            
            $nbTarifs=0;
            $cnt=0;
            
            foreach ($tarifs as $key => $un_tarif) {
                $nb=intval($request->request->get($key.'_nb'));
                $tarifs_commandes[]=$nb;
                $nbTarifs+= $nb;
            }
            echo 'nbTarifs = '+$nbTarifs;
            
            $commandeGlobale->setNbBillets($nbTarifs);
            //Avant de persister il faut récupérer le détail de la commande (nombre de billet-tarif) 
            //sachant de la date est déjà prise en compte ainsi que l'option demi-journée
            
            
            
            $em=$this->getDoctrine()->getManager();
            $em->persist($commandeGlobale);
            $em->flush();
            
            $url = $this->get('router')->generate( 'etape_2', array(
                'id' => $commandeGlobale->getId())
                              );
            return new RedirectResponse($url); 
        }
        return $this->render('OCCoreBundle:Default:initialisation.html.twig', 
                             array(
                                 'initCommande'=>$commandeGlobale,
                                 'form'=>$form->createView(),
                                 'tarifs'=>Tarif::getTarifs(),
                                 'form_tarif'=>$form_tarif->createView(),
                                 'form_commande_tarif' => $form_commandeTarif->createView()
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
