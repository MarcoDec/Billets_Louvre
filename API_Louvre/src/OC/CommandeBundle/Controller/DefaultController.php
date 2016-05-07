<?php

namespace OC\CommandeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OC\CommandeBundle\Entity\CommandeGlobale;
use Symfony\Component\HttpFoundation\RedirectResponse;
    
class DefaultController extends Controller
{
    
    /**
     * @Route("/billet/{id}", name="billet")
     * @Template
     */
    public function paymentAction($id) //id of the order
    {  
    	$tarif="Enfant";
    	$date_reservation= new \Datetime();
    	$nom="Declercq";
    	$prenom="Marc";
    	$id_visiteur=5;

		return $this->render('OCCommandeBundle:billets:billet.html.twig', 
			array(
				'id'=> $id,
				'tarif' => $tarif,
				'dateReservation' => $date_reservation,
				'nom' => $nom,
				'prenom' => $prenom,
				'id_visiteur' => $id_visiteur
				)
			);
	    }
    
}
