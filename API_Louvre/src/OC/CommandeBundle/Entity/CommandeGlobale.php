<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Range(
     *          max="+6 months")
     */
    private $dateReservation;

    /**
     * @var bool
     *
     * @ORM\Column(name="demi_journee", type="boolean", nullable=true)
     */
    private $demiJournee;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_billets", type="integer")
     * @Assert\Range(
     *          min="0",
     *          max="50")
     */
    private $nbBillets;
    
    /**
    * @var date
    *
    * @ORM\Column(name="date_commande", type="date")
    */
    private $date_commande;
    
    /**
    * @ORM\OneToOne(targetEntity="JMS\Payment\CoreBundle\Entity\PaymentInstruction")
    */
    private $paymentInstruction;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sessionId", type="string", length=255)
     */
    private $sessionId;
    
    /**
    * @ORM\OneToMany(targetEntity="OC\CommandeBundle\Entity\CommandeTarif", mappedBy="commande_Globale")
    */
    private $commandes;
    
    
    public function __construct() {
        $this->dateReservation = new \Datetime();
        $this->demiJournee = false;
        $this->nbBillets = 0;
        $this->date_commande = new \Datetime();
        $this->commandes=new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set paymentInstruction
     *
     * @param \JMS\Payment\CoreBundle\Entity\PaymentInstruction $paymentInstruction
     *
     * @return CommandeGlobale
     */
    public function setPaymentInstruction(\JMS\Payment\CoreBundle\Entity\PaymentInstruction $paymentInstruction = null)
    {
        $this->paymentInstruction = $paymentInstruction;

        return $this;
    }

    /**
     * Get paymentInstruction
     *
     * @return \JMS\Payment\CoreBundle\Entity\PaymentInstruction
     */
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }
    
    public function getPrice() {
        return '100.50';
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

    public function setCommandes(\Doctrine\Common\Collections\ArrayCollection $commandes) {
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
}
