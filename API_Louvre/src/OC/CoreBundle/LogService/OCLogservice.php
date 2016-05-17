<?php
// src/OC/CoreBundle/LogService/OCLogservice.php

namespace OC\CoreBundle\LogService;

use OC\CoreBundle\Entity\Mylog;

class OCLogservice
{

  /**
   * Ajoute un élément de log à la base de données
   *
   * @param string $text
   * @return bool
   */
    public function add($controlleur,$log,$datas) {
        $mylog=new Mylog();
        $mylog->setLog($log);
        $mylog->setDatas(serialize($datas));
        $mylog->setDate(new \Datetime());
        $em = $controlleur->getDoctrine()->getManager(); 
        $em->persist($mylog);
        $em->flush($mylog);
    }
}