<?php

namespace OC\CommandeBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

//  MES ENTITES
use OC\CommandeBundle\Entity\CommandeGlobale;
use OC\CommandeBundle\Entity\CommandeTarif;
use OC\CoreBundle\Entity\User;
use OC\CoreBundle\Entity\Mylog;
//  MES FORMULAIRES
use OC\CommandeBundle\Form\CommandeGlobale2Type;
    
class DefaultController extends Controller
{
    /**
    * @Route("/details/{id}", name="etape_2")
    */
    public function data_clientAction(Request $request, $id ) {
    	$mylog = new Mylog();
        $mylog->add($this, 'data_clientAction',$request);
        $commande_globale = new CommandeGlobale();
        $em = $this->getDoctrine()->getManager(); 
        $repo = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $commande_globale=$repo->find($id);
        
        // On vérifie que la commande n'est pas déjà payée (auquel cas on n'affiche pas le formulaire mais on renvoie à la 1ère étape
        if ($commande_globale->getPaid()) {
            $url = $this->get('router')->generate( 'etape_1', array());
            return new RedirectResponse($url);
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
    * @Route("/Paiement/{id}", name="modes_payment")
    */
    public function paiementAction(Request $request, $id)  {
        $mylog = new Mylog();
        $mylog->add($this, 'paiementAction',$request);
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

        if ($commande_globale->getPaid()) {
            $succ="Cette commande a déjà été traitée.<br> Les billets vous ont été envoyés par email.";
        } elseif ($commande_globale->getAmount()==0) {
            $mylog1 = new Mylog();
            $mylog1->add($this, 'Gratuit',$commande_globale);
            // Il n'y a rien à payer (exemple que des billets Gratuit)
            $commande_globale->setStripe(false);
            $commande_globale->setPaid(true);
            $commande_globale->setDateCommande(new \Datetime());
            $em->persist($commande_globale);
            $em->flush();
            $succ="La commande vient d'être traitée.<br>Les billets ont bien été envoyés sur votre email.<br>Merci pour votre confiance, et à bientôt !";
            // Dans ce cas, on créé les billets et on les envoie par mail.
            $billets_filenames = $this->genererBillets($commande_globale);
            $this->sendMail($commande_globale,$billets_filenames);
            // Et pour finir, on remercie le client
        } elseif ($request->isMethod('POST')) { 
            /************************************************
            * Traitement d'un paiement par Stripe
            *************************************************/
            $mylog2 = new Mylog();
            $mylog2->add($this, 'Paiement Stripe',$commande_globale);

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
              $billets_filenames = $this->genererBillets($commande_globale);
              $this->sendMail($commande_globale,$billets_filenames);
              // Ensuite on renverra vers la page de remerciement
            } catch(\Stripe\Error\Card $e) {
                $mylog3 = new Mylog();
                $mylog3->add($this, 'Erreur Stripe',$e);
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
             'business' => $this->container->getParameter('paypal_account')
         )
        ); 
        }

    /**
     * @Route("/{id}/billet/{id_visiteur}", name="billet")
     * @Template
     */
    public function billetAction($id, $id_visiteur) {  
    	// Récupération de la commande et de ses informations spécifiques à passer dans le twig
        $commande_globale = new CommandeGlobale();
        $em = $this->getDoctrine()->getManager(); 
        $repo = $em->getRepository('OCCommandeBundle:CommandeGlobale');
        $commande_globale=$repo->find($id);
        if (null==$commande_globale) {
        	throw new Exception("L'information que vous demandée n'est pas disponible", 1);
        }
        $date_reservation=$commande_globale->getDateReservation();
        $demi_journee=$commande_globale->getDemiJournee();
		// Récupération du visiteur si effectivemet associé à la commande
        $repo_user = $em->getRepository('OCCoreBundle:User');
        $visiteur=$repo_user->find($id_visiteur);
        if (null==$commande_globale) {
        	throw new Exception("L'information que vous demandée n'est pas disponible", 1);
        }

        if ($id!=$visiteur->getCommandeTarif()->getCommandeGlobale()->getId()) {
        	throw new Exception("Incohérence des données détectée !", 1);
        }

		$nom=$visiteur->getNom();
		$prenom=$visiteur->getPrenom();
		$tarif=$visiteur->getCommandeTarif()->getTarif()->getTarifKey();

		return $this->render('OCCommandeBundle:billets:billet.html.twig', 
			array(
				'id'=> $id,
				'tarif' => $tarif,
				'dateReservation' => $date_reservation,
				'nom' => $nom,
				'prenom' => $prenom,
				'id_visiteur' => $id_visiteur,
				'demi_journee' => $demi_journee
				)
		);
	}

    /**
    * @Route("/ipn/", name="paypal_ipn")
    */
    public function ipnAction(Request $request)  {
        //Réception notification de paypal, vérifications et si tout est ok validation
    	$mylog = new Mylog();
        $mylog->add($this, 'ipnAction',$request);
        $logfile = $this->container->getParameter('logfile');

        $email_account=$this->container->getParameter('paypal_account');
        // Construction des paramètres à passer dans l'url de validation
        $req='cmd=_notify_validate';
        foreach ($_POST as $key => $value) {
            $value=urlencode(stripslashes($value));
            $req.="&".$key."=".$value;
        }
        // Construction de l'entête de la réponse à envoyer
        $header="POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header.="Content-Type: application/x-www-form-urlencoded\r\n";
        $header.="Content-Length: ".strlen($req)."\r\n\r\n";
        // Ouverture d'une liaison sécurisée avec sandbox paypal
        //$fp=fsockopen('ssl://sandbox.paypal.com',443, $errno, $errstr, 30);
        $fp = file_get_contents('https://www.sandbox.paypal.com/',443);
        //$fp = file_get_contents('https://www.sandbox.paypal.com/cgi-bin/webscr');
        
        // Récupération des éléments postés dans la requête de notification initiale de Paypal
        $item_name= $_POST['item_name'];
        $item_number=$_POST['item_number'];
        $payment_status = $_POST['payment_status'];
        $payment_amount = $_POST['mc_gross'];
        $payment_currency = $_POST['mc_currency'];
        $txn_id=$_POST['txn_id'];
        $receiver_email = $_POST['receiver_email'];
        $payer_email = $_POST['payer_email'];
        // Conversion sous forme de tableau du paramètre custom envoyé à Paypal dans le formulaire de paiement
        parse_str($_POST['custom'],$custom);


        if (!$fp) {
	    	$mylog1 = new Mylog();
	        $mylog1->add($this, 'Connexion sécurisée Paypal impossible',$fp);
        } else {
	    	$mylog1 = new Mylog();
	        $mylog1->add($this, 'Connexion sécurisée Paypal réussie',$fp);
	        // Envoie et mémorisation de la requête de validation via la liaison sécurisée dans $fp
            fputs($fp, $header.$req);
            while (!eof($fp)) {
            	// Récupération de la réponse par 'ligne' de 1024 de longueur
                $res = fgets($fp,1024);  
                // Comparaison binaire de la ligne avec VERIFIED
                if (strcmp($res, "VERIFIED")==0) { // Si les deux chaines sont identiques
                    $mylog2 = new Mylog();
	        		$mylog2->add($this, 'Connexion VERIFIED',$fp);
                    if ($payment_status == "Completed") {
	                    $mylog3 = new Mylog();
		        		$mylog3->add($this, 'Paiement Paypal complété',$payment_status);
                        if ($email_account == $receiver_email) {
		                    $mylog4 = new Mylog();
			        		$mylog4->add($this, 'Test emails Business OK',$receiver_email);
                            // vérifier que la somme est bonne
                            // Si Ok mettre la commandeGlobale à paid avec paypal = true


                        } else {
		                    $mylog4 = new Mylog();
			        		$mylog4->add($this, 'ECHEC Test emails Business',$receiver_email);
                        }
                    } else {
                        // Echec de paiement...
	                    $mylog3 = new Mylog();
		        		$mylog3->add($this, 'ECHEC Paiement Paypal',$payment_status);
                    }
                } else if (strcmp($res, "INVALID")==0)  {
                    //transaction non valide.
                    $mylog2 = new Mylog();
	        		$mylog2->add($this, 'Transaction NON VALIDE',$fp);
                }
            }
        }
        //$mylog->add($this,'Requête IPN', 'fin');
    }



/**********************************************************************
*
*       FONCTIONS PERSONNALISEES
*
**********************************************************************/

    /**
    * Cette fonction génère l'ensemble des billets relatifs à une commande globale
    */
    private function genererBillets(CommandeGlobale $commande_globale) {
        $mylog = new Mylog();
        $mylog->add($this, 'genererBillets',$commande_globale);
        $commandes = $commande_globale->getCommandes();
        $id=$commande_globale->getId();
        $date_reservation=$commande_globale->getDateReservation();
        $demi_journee=$commande_globale->getDemiJournee();
        $billetsCommandes=array();
        foreach ( $commandes as $key => $commande_tarif) {
            if ($commande_tarif->getQuantity()!=0) {
                $billets_tarif=$this->genereBilletsTarif($commande_tarif, $id, $date_reservation, $demi_journee);
                $billetsCommandes[]=$billets_tarif;
            }
        }
        return $billetsCommandes;
    }
    /**
    * Cette fonction génère l'ensemble des billets relatifs à un tarif donnée d'une commande
    */
    private function genereBilletsTarif(CommandeTarif $commande_tarif, $id, $date_reservation, $demi_journee) {
        $filenames= array();
        $mylog = new Mylog();
        $mylog->add($this, 'genereBilletsTarif',$commande_tarif);
        $controlleur = new Controller();
        if ($commande_tarif->getQuantity()!=0) {
            $tarif=$commande_tarif->getTarif()->getTarifKey();
            $visiteurs = $commande_tarif->getVisiteurs();
            foreach ($visiteurs as $key => $visiteur) {
                $filename = $this->genereBilletVisiteur($visiteur, $id, $tarif, $date_reservation, $demi_journee);
                $filenames[] = $filename;
            }
        }
        return $filenames;
    }

    /**
    * Cette fonction génère le billet pdf du visiteur passé en paramètre.
    */
    private function genereBilletVisiteur(User $visiteur, $id, $tarif, $date_reservation, $demi_journee) {
        $mylog = new Mylog();
        $mylog->add($this, 'genereBilletVisiteur',$visiteur);
        $nom = $visiteur->getNom();
        $prenom = $visiteur->getPrenom();
        $id_visiteur=$visiteur->getId();
        // TODO mettre le code dans les paramètres
        $apikey = $this->container->getParameter('api_pdf_key');

        $urlapi="http://api.html2pdfrocket.com/pdf";
        $hote = $_SERVER['HTTP_HOST'];
        $value = $this->renderView('OCCommandeBundle:billets:billet.html.twig', 
            array(
                'id'=> $id,
                'tarif' => $tarif,
                'dateReservation' => $date_reservation,
                'nom' => $nom,
                'prenom' => $prenom,
                'id_visiteur' => $id_visiteur,
                'demi_journee' =>$demi_journee
            )
        );
        $postdata = http_build_query(
            array(
                'apikey' => $apikey,
                'value' => $value,
                'MarginBottom' => '30',
                'MarginTop' => '20',
                'LowQuality' => false,
                'DisableJavascript' => false,
                'JavascriptDelay' =>1000,
                'UsePrintStylesheet' =>true,
                'MarginLeft' => '20'
            )
        );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context  = stream_context_create($opts);
        $result = file_get_contents('http://api.html2pdfrocket.com/pdf', false, $context);
        $filename ='Commande_'.$id.'_tarif_'.$tarif.'_visiteur_'.$id_visiteur.'.pdf';
        file_put_contents( $filename, $result);
        return $filename;
    }

    /**
    * Cette fonction prépare et envoie le mail avec les billets à l'acheteur de la commande passée en paramètre
    *
    */
    private function sendMail($commande_globale, $billets_filenames) {
        $message = \Swift_Message::newInstance()
                ->setSubject('[Le Louvre] Vos Billets')
                ->setFrom(array($this->container->getParameter('mailer_user') => 'Le Louvre - Service Client'))
                ->setTo($commande_globale->getClient()->getEmail())
                ->setContentType("text/html")
                ->setBody("<p><strong>Bonjour</strong><br>Merci d'avoir commandé des billets pour le Louvre sur notre site.<br>".$commande_globale->toString($this)."<br>Veuillez trouver ci-joint vos billets</p>");
        $mailer= $this->get('mailer');
        // Attacher les billets générés au mail.
        foreach ($billets_filenames as $key => $billets_tarif) {
            //$billets_tarif est un tableau contenant l'ensemble des billets émis pour un des tarifs sélectionné
            foreach ($billets_tarif as $key => $billet) {
                $message -> attach(\Swift_Attachment::fromPath('../web/'.$billet));
            }
        }
        
        $mailer->send($message);
    }

}
