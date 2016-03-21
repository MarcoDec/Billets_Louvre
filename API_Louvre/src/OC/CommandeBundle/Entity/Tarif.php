<?php

namespace OC\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarif
 *
 * @ORM\Table(name="tarif")
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

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

    public static final function getTarifs() {
        return array(
            'Normal' => array(
                'prix'=>16,
                'description'=> 'Pour les plus de 12 ans et moins de 60 ans',
                'nbBillets'=>1
            ),
            'Senior' => array(
                'prix'=>12,
                'description'=> 'Pour les 60 ans et plus',
                'nbBillets'=>1
            ),
            'Enfant' => array(
                'prix'=>8,
                'description'=> 'Pour les plus de 4 ans et moins de 12 ans',
                'nbBillets'=>1
            ),
            'Gratuit' => array(
                'prix'=>0,
                'description'=> 'Gratuit pour les moins de 4 ans',
                'nbBillets'=>1
            ),
            'Réduit' => array(
                'prix'=>10,
                'description'=> 'Sur présentation des justificatifs',
                'nbBillets'=>1
            ),
            'Famille' => array(
                'prix'=>35,
                'description'=> 'Pour 2 adultes et 2 enfants d\'une même famille',
                'nbBillets'=>4
            )
        );
    }
    
    public function check(Commande $commandes) {
        
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Tarif
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
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
}

