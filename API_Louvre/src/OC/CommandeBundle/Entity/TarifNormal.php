<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifNormal
 *
 * @ORM\Table(name="tarif_normal")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\TarifNormalRepository")
 */
class TarifNormal
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

