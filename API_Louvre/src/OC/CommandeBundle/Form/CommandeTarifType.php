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
                'label' => 'Quantité'
            ))
            /*->add('tarif', ChoiceType::class, array(
                'choices' => array(
                        '0' => 'Tarif 0',
                        '1' => 'Tarif 1',
                        '2' => 'Tarif 2',
                    ),
                'multiple' => false,
                'expanded' => false
            )
                 )*/
            
            /*->add('tarif', ChoiceType::class, [
                'choices' => [
                    new Tarif('Tarif 0'),
                    new Tarif('Tarif 1'),
                    new Tarif('Tarif 2'),
                    new Tarif('Tarif 3')
                ],
                'choices_as_values' => true,
                'choice_label' => function($tarif, $key, $index) {
                    return strtoupper($tarif->getTarifKey());
                },
                'choice_attr' => function($tarif, $key, $index) {
                    return ['class' => 'tarif_'.strtolower($tarif->getTarifKey())];
                }
            ]
            
            )*/
            ->add('tarif', EntityType::class, array(
                'class' => 'OCCommandeBundle:Tarif',
                'choice_label' => 'tarif_key',
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
