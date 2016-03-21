<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeGlobaleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', 'date')
            ->add('demiJournee')
            ->add('nbBillets', 'integer')
            ->add('Reserver', 'submit', array('label' => 'Réserver'));
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
