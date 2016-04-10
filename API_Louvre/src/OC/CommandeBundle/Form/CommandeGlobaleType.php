<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use OC\CommandeBundle\Entity\CommandeTarif;

class CommandeGlobaleType extends AbstractType
{
    /* Attribute de la classe CommandeGlobale
    id                      integer
    dateReservation         date
    demiJournee             boolean
    nbBillets               integer
    date_commande           datetime
    paymentInstruction      JMS\Payment\CoreBundle\Entity\PaymentInstruction
    sessionId               string
    commandes               OC\CommandeBundle\Entity\CommandeTarif
    */
    
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', DateType::class, array(
                'label' => 'Date réservation'))
            ->add('demiJournee', CheckboxType::class, array(
                'label' => '1/2 journée -->', 
                'required'=> false))
            ->add('commandes', CollectionType::class, [
                'type' => new CommandeTarifType,
                'allow_add' => false,
                'allow_delete' => false])
            ->add('nbBillets', IntegerType::class, array(
                'label'=>'Total', 
                'attr'=> array('style'=> 'width:100%;', 'min'=>'0'), 
                'disabled' => false))
            ->add('Reserver', SubmitType::class , array(
                'label' => 'Cliquez ici pour Réserver', 
                'attr' => array('style' => 'width:100%;')))
            ;
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\CommandeBundle\Entity\CommandeGlobale'
        ));
    }
    
    /**
    * @return string
    */
    public function getName() {
        return 'oc_commandebundle_CommandeGlobale';
    }
}
