<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarif
 *
 * @ORM\Table(name="tarifs")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\TarifRepository")
 */
class Tarif
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
     * @ORM\Column(name="tarif_key", type="string", length=64)
     */
    private $tarif_key;
    
    /**
    * @var string
    *
    * @ORM\Column(name="description", type="string", length=255)
    */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="nbBillets", type="smallint")
     */
    private $nbBillets;

    /**
     * @var string
     *
     * @ORM\Column(name="cout", type="decimal", precision=10, scale=0)
     */
    private $cout;
    
    /**
    * @var bool
    *
    * @ORM\Column(name="requireBirthday", type="boolean")
    */
    private $requireBirthday;
    
    public function __construct($key='new tarif') {
        $this->tarif_key=$key;
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
     * Set nbBillets
     *
     * @param integer $nbBillets
     *
     * @return Tarif
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
     * Set cout
     *
     * @param string $cout
     *
     * @return Tarif
     */
    public function setCout($cout)
    {
        $this->cout = $cout;

        return $this;
    }

    /**
     * Get cout
     *
     * @return string
     */
    public function getCout()
    {
        return $this->cout;
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tarif
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set tarifKey
     *
     * @param string $tarifKey
     *
     * @return Tarif
     */
    public function setTarifKey($tarifKey)
    {
        $this->tarif_key = $tarifKey;

        return $this;
    }

    /**
     * Get tarifKey
     *
     * @return string
     */
    public function getTarifKey()
    {
        return $this->tarif_key;
    }

    /**
     * Set requireBirthday
     *
     * @param boolean $requireBirthday
     *
     * @return Tarif
     */
    public function setRequireBirthday($requireBirthday)
    {
        $this->requireBirthday = $requireBirthday;

        return $this;
    }

    /**
     * Get requireBirthday
     *
     * @return boolean
     */
    public function getRequireBirthday()
    {
        return $this->requireBirthday;
    }
}
