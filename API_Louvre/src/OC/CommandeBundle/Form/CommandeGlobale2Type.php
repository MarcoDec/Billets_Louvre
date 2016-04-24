<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\CoreBundle\Form\ClientType;
use OC\CommandeBundle\Form\CommandeTarif2Type;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CommandeGlobale2Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', ClientType::class)
            ->add('commandes', CollectionType::class, array('entry_type' => CommandeTarif2Type::class))
            /*->add('Reserver', SubmitType::class , array(
                'label' => 'Cliquez ici pour Réserver', 
                'attr' => array('style' => 'width:100%;')))*/
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
