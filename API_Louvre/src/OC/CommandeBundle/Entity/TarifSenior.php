<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifSenior
 *
 * @ORM\Table(name="tarif_senior")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\TarifSeniorRepository")
 */
class TarifSenior
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

