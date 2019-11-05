<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_by', ChoiceType::class, [
                'placeholder' => 'Choisissez le champs à trier',
                'choices'  => [
                    'Date' => 'createdAt',
                    'Titre' => 'title',
                ],
            ])
            ->add('order', ChoiceType::class, [
                'placeholder' => 'Choisissez l\'ordre du tri',
                'choices'  => [
                    'Croissant'   => 'ASC',
                    'Décroissant' => 'DESC',
                ],
            ])
            ->add('count', NumberType::class, [
                //'placeholder' => 'Nombre maximal à afficher',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
