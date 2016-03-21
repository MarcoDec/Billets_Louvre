<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class TarifFamille extends Tarif
{
    public function __construct() {
        $this->nom="Tarif famille pour 2 adultes et 2 enfants";
        $this->nbBillets=4;
        $this->cout=35;
    }
    
    public function check(Commande commandes) {
        if
    }
}

