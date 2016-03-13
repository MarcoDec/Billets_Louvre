<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\CommandeRepository")
 */
class Commande
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
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255)
     */
    private $clientName;

    /**
     * @var string
     *
     * @ORM\Column(name="client_first_name", type="string", length=255)
     */
    private $clientFirstName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="client_birtday", type="date")
     */
    private $clientBirtday;

    
    /**
    *
    * @ORM\ManyToOne(targetEntity="OC\CommandeBundle\Entity\CommandeGlobale")
    * @ORM\JoinColumn(nullable=false)
    */
    private $commandeGlobale;
    

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
     * Set clientName
     *
     * @param string $clientName
     *
     * @return Commande
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set clientFirstName
     *
     * @param string $clientFirstName
     *
     * @return Commande
     */
    public function setClientFirstName($clientFirstName)
    {
        $this->clientFirstName = $clientFirstName;

        return $this;
    }

    /**
     * Get clientFirstName
     *
     * @return string
     */
    public function getClientFirstName()
    {
        return $this->clientFirstName;
    }

    /**
     * Set clientBirtday
     *
     * @param \DateTime $clientBirtday
     *
     * @return Commande
     */
    public function setClientBirtday($clientBirtday)
    {
        $this->clientBirtday = $clientBirtday;

        return $this;
    }

    /**
     * Get clientBirtday
     *
     * @return \DateTime
     */
    public function getClientBirtday()
    {
        return $this->clientBirtday;
    }

    /**
     * Set commandeGlobale
     *
     * @param \OC\CommandeBundle\Entity\CommandeGlobale $commandeGlobale
     *
     * @return Commande
     */
    public function setCommandeGlobale(\OC\CommandeBundle\Entity\CommandeGlobale $commandeGlobale)
    {
        $this->commandeGlobale = $commandeGlobale;

        return $this;
    }

    /**
     * Get commandeGlobale
     *
     * @return \OC\CommandeBundle\Entity\CommandeGlobale
     */
    public function getCommandeGlobale()
    {
        return $this->commandeGlobale;
    }
}