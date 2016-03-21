<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifGratuit
 *
 * @ORM\Table(name="tarif_gratuit")
 * @ORM\Entity(repositoryClass="OC\CommandeBundle\Repository\TarifGratuitRepository")
 */
class TarifGratuit
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

