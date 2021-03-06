<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OC\CommandeBundle\Entity\CommandeTarif;

/**
 * CommandeGlobale
 *
 * @ORM\Table(name="commande_globale")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\CommandeGlobaleRepository")
 */
class CommandeGlobale
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reservation", type="date")
     * 
     */
    private $dateReservation;       // Rempli à l'étape 1

    /**
     * @var bool
     *
     * @ORM\Column(name="demi_journee", type="boolean", nullable=true)
     */
    private $demiJournee;           // Rempli à l'étape 1

    /**
     * @var int
     *
     * @ORM\Column(name="nb_billets", type="integer")
     * @Assert\Range(
     *          min="1",
     *          max="50")
     */
    private $nbBillets;             // Rempli à l'étape 1
    
    /**
    * @var date
    *
    * @ORM\Column(name="date_commande", type="datetime")
    */
    private $date_commande;         // Rempli à l'étape 1
    
    /**
     * @var string
     *
     * @ORM\Column(name="sessionId", type="string", length=255, nullable=true)
     */
    private $sessionId;             // Rempli à l'étape 1
    
    /**
    * @ORM\OneToMany(targetEntity="OC\CommandeBundle\Entity\CommandeTarif", mappedBy="commande_Globale", cascade={"persist"})
    */
    private $commandes;              // Rempli à l'étape 1
    
    /**
    * @ORM\ManyToOne(targetEntity="OC\CoreBundle\Entity\User")
    */
    private $client;
    

    /**
    * @ORM\Column(name="stripe", type="boolean", nullable=true)
    */
    private $stripe;

    /**
    * @ORM\Column(name="paid", type="boolean", nullable=true)
    */
    public $paid;


    public function __construct() {
        $this->dateReservation = new \Datetime();
        $this->demiJournee = false;
        $this->nbBillets = 0;
        $this->date_commande = new \Datetime();
        $this->commandes=new ArrayCollection();
        $this->stripe=false;
        $this->paid=false;
    }
    
    public function validate(ExecutionContextInterface $context) {

        $dateResa=$this->getDateReservation();
        $demiJournee=$this->getDemiJournee();
        
        $err = array();
        $num_jour=$dateResa['w'];
        
        if ($num_jour==0 || $num_jour==6) {
            $err[]='Le Louvre est fermé le samedi et le dimanche. Merci de choisir un autre jour';
        }
        if ($err.length!=0) {
            $context->buildViolation('Erreur dans la saisie des informations')
                ->atPath('tutu')
                ->addViolation();
        }
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set dateReservation
     *
     * @param \DateTime $dateReservation
     *
     * @return CommandeGlobale
     */
    public function setDateReservation($dateReservation)
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    /**
     * Get dateReservation
     *
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * Set demiJournee
     *
     * @param boolean $demiJournee
     *
     * @return CommandeGlobale
     */
    public function setDemiJournee($demiJournee)
    {
        $this->demiJournee = $demiJournee;

        return $this;
    }

    /**
     * Get demiJournee
     *
     * @return bool
     */
    public function getDemiJournee()
    {
        return $this->demiJournee;
    }

    /**
     * Set nbBillets
     *
     * @param integer $nbBillets
     *
     * @return CommandeGlobale
     */
    public function setNbBillets($nbBillets)
    {
        $this->nbBillets = $nbBillets;

        return $this;
    }

    /**
     * Get nbBillets
     *
     * @return int
     */
    public function getNbBillets()
    {
        return $this->nbBillets;
    }

    /**
     * Set emailAcheteur
     *
     * @param string $emailAcheteur
     *
     * @return CommandeGlobale
     */
    public function setEmailAcheteur($emailAcheteur)
    {
        $this->email_acheteur = $emailAcheteur;

        return $this;
    }

    /**
     * Get emailAcheteur
     *
     * @return string
     */
    public function getEmailAcheteur()
    {
        return $this->email_acheteur;
    }

    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return CommandeGlobale
     */
    public function setDateCommande($dateCommande)
    {
        $this->date_commande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->date_commande;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     *
     * @return CommandeGlobale
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Add commande
     *
     * @param \OC\CommandeBundle\Entity\Commande $commande
     *
     * @return CommandeGlobale
     */
    public function addCommande(\OC\CommandeBundle\Entity\CommandeTarif $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    public function setCommandes(ArrayCollection $commandes) {
        $this->commandes=$commandes;
        foreach ($commandes as $commande) {
            $commande->setCommandeGlobale($this);
        }
    }
    
    /**
     * Remove commande
     *
     * @param \OC\CommandeBundle\Entity\Commande $commande
     */
    public function removeCommande(\OC\CommandeBundle\Entity\CommandeTarif $commande)
    {
        $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Set client
     *
     * @param \OC\CoreBundle\Entity\User $client
     *
     * @return CommandeGlobale
     */
    public function setClient(\OC\CoreBundle\Entity\User $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \OC\CoreBundle\Entity\User
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * Set stripe
     *
     * @param boolean $stripe
     *
     * @return CommandeGlobale
     */
    public function setStripe($stripe)
    {
        $this->stripe = $stripe;
        return $this;
    }

    /**
     * Get stripe
     *
     * @return boolean
     */
    public function getStripe()
    {
        return $this->stripe;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     *
     * @return CommandeGlobale
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /******************************************
    *
    *   Mes fonctions spéciales               *
    *
    *******************************************/


    /**
    * Cette fonction retourne le prix en centime (x100)
    * On en a besoin pour Stripe
    */
    public function getAmount() {

        return $this->getPrice()*100;
    }
    
    /**
    * Cette fonction retourne le prix en euro
    * Utilisé pour Paypal
    */
    public function getPrice() {
        $price=0;
        foreach ($this->commandes as $commande_tarif) {
            $cout_tarif = $commande_tarif->getTarif()->getCout();
            $quantity = $commande_tarif->getQuantity();
            $price+=$cout_tarif*$quantity;
        }
        return $price;
    }

    public function isPaid() {
        return (($this->paid) ? 'Payé' : 'Non payé');
    }

    public function getDesc() {
        $desc="Vous avez commandé ";
        $desc.=$this->getNbBillets()." billet(s) pour le louvre ";
        $desc.="pour le ".($this->getDateReservation()->format('Y-m-d H:i:s'));
        return $desc;
    }

    public function toString() {
        $mess="<br><h2>RECAPITULATIF DE VOTRE COMMANDE</h2>"
        ."<br>N°".strval($this->id)
        ."<br>Pour la ".(($this->demiJournee) ? 'demi-journée ' : 'journée complète ')
        ."du ".($this->dateReservation->format('Y-m-d'))
        ."<br>Nombre de billets achetés :".strval($this->nbBillets)
        ."<br>Le paiement a bien été effectuée."
        ."<br><br><h3>Détails de vos billets</h3>";
        if (count($this->commandes)!=0) {
            foreach ($this->commandes as $key => $commande) {
                if ($commande->getQuantity()!=0) {
                    $mess=$mess.'<br>-------------------------'.($commande->toString()).'<br>-------------------------';
                }   
            }
        }
        return $mess;
    }

    public function getCommandeWithTarif($id_tarif) {
        foreach ($this->commandes as $commande) {
            if ($commande->getTarif()->getId() == $id_tarif) {
                return $commande;
            }
        }
        return null;
    }
    
    public function hasTarif(\OC\CommandeBundle\Entity\Tarif $tarif) {
        foreach ($this->commandes as $commande) {
            if ($commande->getTarif()->getId() == $tarif->getId()) {
                return true;
            }
        }
        return false;
    }


    /**
    * Cette fonction initalise les items de la commande pour chacun des tarifs existant dans la base
    * les quantités sont évidemment initialisée à 0
    */
    public function initCommandes($control) {
        // On initialise les commandes
        $tarifsRepository= $control->getDoctrine()->getManager()->getRepository('OCCommandeBundle:Tarif');
        $list_tarifs=$tarifsRepository->findAll();
        foreach($list_tarifs as $tarif) {
            $commande_tarif=new CommandeTarif();
            $commande_tarif->setCommandeGlobale($this);
            $commande_tarif->setTarif($tarif);
            $commande_tarif->setQuantity(0);
            $this->addCommande($commande_tarif);
        }
    }
}
