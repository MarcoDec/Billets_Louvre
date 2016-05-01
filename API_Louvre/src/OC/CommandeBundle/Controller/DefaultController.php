<?php

namespace OC\CommandeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\DiExtraBundle\Annotation as DI;
use OC\CommandeBundle\Entity\CommandeGlobale;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\Payment\CoreBundle\PluginController\Result;
    
class DefaultController extends Controller
{
    /** @DI\Inject */
    private $request;
    
    /** @DI\Inject */
    private $router;
    
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $em;
    
    /** @DI\Inject("payment.plugin_controller") */
    private $ppc;
    
    /**
     * @Route("/{id}", name="paiement")
     * @Template
     */
    public function paymentAction(CommandeGlobale $commandeGlobale) //id of the order
    {  
        $form = $this->getFormFactory()->create('jms_choose_payment_method', null, array(
            'amount'   => $commandeGlobale->getPrice(),
            'currency' => 'EUR',
            'default_method' => 'payment_paypal', // Optional
            'predefined_data' => array(
                    'paypal_express_checkout' => array(
                        'return_url' => $this->router->generate('payment_complete', array(
                            'id' =>$commandeGlobale->getId()
                        ), true),
                        'cancel_url' => $this->router->generate('payment_cancel', array(
                            'id' => $commandeGlobale->getId()
                        ), true)
                    ),
                    'stripe_checkout' => array(
                        'description' => 'My Product MD',
                    ),
            ),
        ));
 
        if ('POST' === $this->request->getMethod()) {
            $form->handleRequest($this->request);
 
    // Once the Form is validate, you update the order with payment instruction
            if ($form->isValid()) {
                $instruction = $form->getData();
                $this->ppc->createPaymentInstruction($instruction);
                $commandeGlobale->setPaymentInstruction($instruction);
                $this->em->persist($commandeGlobale);
                $this->em->flush($commandeGlobale);
                // now, let's redirect to payment_complete with the order id
                return new RedirectResponse($this->router->generate('payment_complete', array('id' => $commandeGlobale->getId(), )));
            }
        }
        return $this->render('OCCommandeBundle:Default:index.html.twig',array('form' => $form->createView() , 'id' => $commandeGlobale->getId(), 'commandeGlobale' => $commandeGlobale));
    }
    
/**
     * @Route("/complete/{id}", name = "payment_complete")
     */
    public function completeAction(CommandeGlobale $commandeGlobale) 
    {
        
        /**if ( $id != 0 ){
            $commandeGlobale = $this->getDoctrine()->getRepository("OCCommandeBundle:CommandeGlobale")->find($id);}*/
 
        $instruction = $commandeGlobale->getPaymentInstruction();
        if (null === $pendingTransaction = $instruction->getPendingTransaction()) {
            $payment = $this->ppc->createPayment($instruction->getId(), $instruction->getAmount() - $instruction->getDepositedAmount());
        } else {
            $payment = $pendingTransaction->getPayment();
        }
 
        $result = $this->ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        if (Result::STATUS_PENDING === $result->getStatus()) {
            $ex = $result->getPluginException();
            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();
                if ($action instanceof VisitUrl) {
                    return new RedirectResponse($action->getUrl());
                }
                throw $ex;
            }
        } else if (Result::STATUS_SUCCESS !== $result->getStatus()) {
            throw new \RuntimeException('Transaction was not successful: '.$result->getReasonCode());
        }
        // if successfull, i render my order validation template
        return $this->render('OCCommandeBundle:Paiement:validation.html.twig',array('order'=>$commandeGlobale ));
 
    }
 
    /** @DI\LookupMethod("form.factory") */
    protected function getFormFactory() { }
 
    /**
     * @Route("/cancel", name = "payment_cancel")
     */
    public function CancelAction( )
    {
        $this->get('session')->getFlashBag()->add('info', 'Transaction annulée.');
       return $this->render('OCCommandeBundle:Paiement:cancel.html.twig',array('order'=>$commandeGlobale ));
        //return $this->redirect($this->generateUrl('OCCommandeBundle:Paiement:cancel.html.twig'));
    }
    
}
