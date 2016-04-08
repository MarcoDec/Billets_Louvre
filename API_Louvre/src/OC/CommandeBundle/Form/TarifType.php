<?php

namespace OC\CommandeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class TarifType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tarif_key', TextType::class, array(
                'label' => 'Saisissez ici le nom du nouveau tarif'
            ) 
                 )
            ->add('description', TextareaType::class, array(
                
                'label' => 'Saisissez ici les conditions d\'application du tarif'
            ))
            ->add('nbBillets', IntegerType::class, array(
                'label' => 'Saisissez ici le nombre de places vendues pour ce tarif'
            ) )
            ->add('cout', MoneyType::class, array(
                'currency' =>'EUR',
                'label' => 'Saisissez ici le prix de ce tarif'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\CommandeBundle\Entity\Tarif'
        ));
    }
}
