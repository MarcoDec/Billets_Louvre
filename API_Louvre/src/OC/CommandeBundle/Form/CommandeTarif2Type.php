<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use OC\CoreBundle\Form\VisitorType;
use OC\CommandeBundle\Form\TarifType;

class CommandeTarif2Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tarif', TarifType::class)
            ->add('quantity', TextType::class, array('label' => 'Saisissez ici la quantité'))
            ->add('visiteurs', CollectionType::class, array('entry_type' => VisitorType::class))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\CommandeBundle\Entity\CommandeTarif'
            //'data_class' => null
        ));
    }
     /**
    * @return string
     */
    public function getName()
    {
        return 'oc_commandebundle_commandetarif';
    }

    
}
