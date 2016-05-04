<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CommandeTarif
 *
 * @ORM\Table(name="commande_tarif")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\CommandeTarifRepository")
 */
class CommandeTarif
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
    * @ORM\ManyToOne(targetEntity="OC\CommandeBundle\Entity\CommandeGlobale", inversedBy="commandes")
    * @ORM\JoinColumn(nullable=false)
    *
    */
    private $commande_Globale;
    
    /**
    * @ORM\ManyToOne(targetEntity="OC\CommandeBundle\Entity\Tarif")
    *
    */
    private $tarif;
    
    /**
    * @ORM\OneToMany(targetEntity="OC\CoreBundle\Entity\User", mappedBy="commandeTarif")
    */
    private $visiteurs;
    
    public function __construct() {
        $this->visiteurs = new ArrayCollection();
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CommandeTarif
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set commandeGlobale
     *
     * @param \OC\CommandeBundle\Entity\CommandeGlobale $commandeGlobale
     *
     * @return CommandeTarif
     */
    public function setCommandeGlobale(\OC\CommandeBundle\Entity\CommandeGlobale $commandeGlobale)
    {
        $this->commande_Globale = $commandeGlobale;

        return $this;
    }

    /**
     * Get commandeGlobale
     *
     * @return \OC\CommandeBundle\Entity\CommandeGlobale
     */
    public function getCommandeGlobale()
    {
        return $this->commande_Globale;
    }

    /**
     * Set tarif
     *
     * @param \OC\CommandeBundle\Entity\Tarif $tarif
     *
     * @return CommandeTarif
     */
    public function setTarif(\OC\CommandeBundle\Entity\Tarif $tarif)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return \OC\CommandeBundle\Entity\Tarif
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    public function setVisiteurs( $visiteurs) {
        $this->visiteurs = $visiteurs;
    }
    
    /**
     * Add visiteur
     *
     * @param \OC\CoreBundle\Entity\User $visiteur
     *
     * @return CommandeTarif
     */
    public function addVisiteur(\OC\CoreBundle\Entity\User $visiteur)
    {
        $this->visiteurs[] = $visiteur;
        $visiteur->setCommandeTarif($this);
        return $this;
    }

    /**
     * Remove visiteur
     *
     * @param \OC\CoreBundle\Entity\User $visiteur
     */
    public function removeVisiteur(\OC\CoreBundle\Entity\User $visiteur)
    {
        $this->visiteurs->removeElement($visiteur);
    }

    /**
     * Get visiteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisiteurs()
    {  
        return $this->visiteurs;
    }

    public function toString() {
        $mess="<br>    ---> Quantité :".strval($this->quantity)
        ."<br>    ---> CommandeGlobale de référence :".strval($this->commande_Globale->getId())
        ."<br>    ---> Tarif :".$this->tarif->getTarifKey()
        ."<br>    ---> Nombre de visiteurs : ".strval(count($this->visiteurs));

        return $mess;
    }
}
