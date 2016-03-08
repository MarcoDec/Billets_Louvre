<?php

namespace MDPlanningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DateLouvre
 *
 * @ORM\Table(name="date_louvre")
 * @ORM\Entity(repositoryClass="MDPlanningBundle\Repository\DateLouvreRepository")
 */
class DateLouvre
{
    private static final $MAX_BILLETS=1000;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_opened", type="boolean")
     */
    private $isOpened;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_billets", type="integer")
     */
    private $nbBillets;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="theDate", type="datetime")
     */
    private $theDate;
    
    
    public function __construct() {
        $this->nbBillets=0;
        $this->isOpened=true;
        $this->theDate=new \Datetime();
    }
    
    public function book($nbBillets) {
        $total = $this->nbBillets+$nbBillets;
        if ($total >= DateLouvre:MAX_BILLETS) {
            return false;
        } else {
            $this->nbBillets=$total;
            return true;
        }
    }
    
    public function unBook($nbBillets) {
        $total = $this->nbBillets-$nbBillets;
        if ($total<0) {
            return false;
        } else {
            $this->nbBillets=$total;
            return true;
        }
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isOpened
     *
     * @param boolean $isOpened
     * @return DateLouvre
     */
    public function setIsOpened($isOpened)
    {
        $this->isOpened = $isOpened;

        return $this;
    }

    /**
     * Get isOpened
     *
     * @return boolean 
     */
    public function getIsOpened()
    {
        return $this->isOpened;
    }

    /**
     * Set nbBillets
     *
     * @param integer $nbBillets
     * @return DateLouvre
     */
    public function setNbBillets($nbBillets)
    {
        $this->nbBillets = $nbBillets;

        return $this;
    }

    /**
     * Get nbBillets
     *
     * @return integer 
     */
    public function getNbBillets()
    {
        return $this->nbBillets;
    }

    /**
     * Set theDate
     *
     * @param \DateTime $theDate
     * @return DateLouvre
     */
    public function setTheDate($theDate)
    {
        $this->theDate = $theDate;

        return $this;
    }

    /**
     * Get theDate
     *
     * @return \DateTime 
     */
    public function getTheDate()
    {
        return $this->theDate;
    }
}
