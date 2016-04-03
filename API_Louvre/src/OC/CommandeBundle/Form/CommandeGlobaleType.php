<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CommandeGlobaleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', 'date',array('label' => 'Date réservation'))
            ->add('demiJournee', CheckboxType::class,array('label' => '1/2 journée -->', 'required'=> false,))
            ->add('Reserver', 'submit', array('label' => 'Cliquez ici pour Réserver', 'attr' => array('style' => 'width:100%;')));
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
