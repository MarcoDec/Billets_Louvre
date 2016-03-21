<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifReduit
 *
 * @ORM\Table(name="tarif_reduit")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\TarifReduitRepository")
 */
class TarifReduit
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

