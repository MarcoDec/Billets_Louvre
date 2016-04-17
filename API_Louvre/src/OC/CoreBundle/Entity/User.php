<?php

namespace OC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="OC\CoreBundle\Repository\UserRepository")
 */
class User
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
     * @Assert\Length(
     *      min = 3,
    *       max = 50,
    *       minMessage = "Veuillez entrer au moins {{ limit }} caractères.",
    *       maxMessage = "Veuillez saisir moins de {{ limit }} caractères."
    *   )
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @Assert\Length(
     *      min = 3,
    *       max = 50,
    *       minMessage = "Veuillez entrer au moins {{ limit }} caractères.",
    *       maxMessage = "Veuillez saisir moins de {{ limit }} caractères."
    *   )
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * @var \DateTime
     *
     * @ORM\ManyToOne(targetEntity="OC\CommandeBundle\Entity\CommandeTarif", inversedBy="visiteurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commandeTarif;

    public function __construct() {
        $this->nom="";
        $this->prenom="";
        $this->email="";
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
     * @return User
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set commandeTarif
     *
     * @param \OC\CommandeBundle\Entity\CommandeTarif $commandeTarif
     *
     * @return User
     */
    public function setCommandeTarif(\OC\CommandeBundle\Entity\CommandeTarif $commandeTarif)
    {
        $this->commandeTarif = $commandeTarif;

        return $this;
    }

    /**
     * Get commandeTarif
     *
     * @return \OC\CommandeBundle\Entity\CommandeTarif
     */
    public function getCommandeTarif()
    {
        return $this->commandeTarif;
    }
}
