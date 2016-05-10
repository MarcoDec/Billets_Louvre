<?php

namespace OC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mylog
 *
 * @ORM\Table(name="mylog")
 * @ORM\Entity(repositoryClass="OC\CoreBundle\Repository\MylogRepository")
 */
class Mylog
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
     * @ORM\Column(name="log", type="string", length=255)
     */
    private $log;

    /**
     * @var string
     *
     * @ORM\Column(name="datas", type="text")
     */
    private $datas;

    /**
    *
    * @ORM\Column(name="date", type="datetime")
    */
    private $date;

    /**************************************************
    * Mes fonctions personnalisées
    *           DEBUT
    ***************************************************/
    public function add($controlleur,$log,$datas) {
        
        $this->log=$log;
        $this->datas=serialize($datas);
        $this->date = new \Datetime();
        $em = $controlleur->getDoctrine()->getManager(); 
        $em->persist($this);
        $em->flush($this);
    }

    /**************************************************
    * Mes fonctions personnalisées
    *           FIN
    ***************************************************/
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
     * Set log
     *
     * @param string $log
     *
     * @return Mylog
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set datas
     *
     * @param string $datas
     *
     * @return Mylog
     */
    public function setDatas($datas)
    {
        $this->datas = $datas;

        return $this;
    }

    /**
     * Get datas
     *
     * @return string
     */
    public function getDatas()
    {
        return $this->datas;
    }
}
