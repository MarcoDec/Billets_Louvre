<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use OC\CommandeBundle\Entity\CommandeTarif;

class CommandeGlobaleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', 'date', array(
                'label' => 'Date réservation'))
            ->add('demiJournee', CheckboxType::class, array(
                'label' => '1/2 journée -->', 
                'required'=> false,))
            ->add('Reserver', 'submit', array(
                'label' => 'Cliquez ici pour Réserver', 
                'attr' => array('style' => 'width:100%;')))
            ->add('commandes', CollectionType::class, [
                'type' => new CommandeTarifType,
                'allow_add' => false,
                'allow_delete' => false
            ])
            ->add('nbBillets', IntegerType::class, array(
                'label'=>'Total', 
                'attr'=> array('style'=> 'width:100%;'), 
                'disabled' => true))
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
