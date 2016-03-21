<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifEnfant
 *
 * @ORM\Table(name="tarif_enfant")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\TarifEnfantRepository")
 */
class TarifEnfant
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

