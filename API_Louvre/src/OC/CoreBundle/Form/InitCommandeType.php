<?php

namespace OC\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InitCommandeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theDate', 'datetime')
            ->add('nbBillets', 'integer')
            ->add('demiJournee')
            ->add('Reserver', 'submit', array('label' => 'Réserver'));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\CoreBundle\Entity\InitCommande'
        ));
    }
    
    /**
    * @return string
    */
    public function getName() {
        return 'oc_corebundle_initCommande';
    }
    
}
