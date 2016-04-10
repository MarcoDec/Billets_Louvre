<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OC\CommandeBundle\Form\TarifType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\CommandeBundle\Entity\Tarif;

class CommandeTarifType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class, array(
                'label' => 'Quantité',
                'disabled' => true
            ))
            ->add('tarif', EntityType::class, array(
                'class' => 'OCCommandeBundle:Tarif',
                'choice_label' => 'tarif_key',
                'disabled' => true,
                'attr' => array('style'=>'width:100%;')
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\CommandeBundle\Entity\CommandeTarif'
        ));
    }
}
