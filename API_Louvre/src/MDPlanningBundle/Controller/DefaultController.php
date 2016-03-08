<?php

namespace MDPlanningBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/HW/")
     */
    public function indexAction()
    {
        return $this->render('MDPlanningBundle:Default:index.html.twig');
    }
}
