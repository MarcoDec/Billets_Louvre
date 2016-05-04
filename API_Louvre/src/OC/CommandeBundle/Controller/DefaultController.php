<?php

namespace OC\CommandeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OC\CommandeBundle\Entity\CommandeGlobale;
use Symfony\Component\HttpFoundation\RedirectResponse;
    
class DefaultController extends Controller
{
    
    /**
     * @Route("/{id}", name="paiement")
     * @Template
     */
    public function paymentAction(CommandeGlobale $commandeGlobale) //id of the order
    {  }
    
}
