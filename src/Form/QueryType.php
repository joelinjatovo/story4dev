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
        $datas = isset($options['datas'])&&is_array($options['datas'])?$options['datas']:['order_by'=>'createdAt','order'=>'ASC','count'=>10];
        
        $builder
            ->add('order_by', ChoiceType::class, [
                'placeholder' => 'Choisissez une option',
                'choices'  => [
                    'Date' => 'createdAt',
                    'Titre' => 'title',
                ],
                'data'  => $datas['order_by'],
            ])
            ->add('order', ChoiceType::class, [
                'placeholder' => 'Choisissez une option',
                'choices'  => [
                    'Croissant'   => 'ASC',
                    'Décroissant' => 'DESC',
                ],
                'data'  => $datas['order_by'],
            ])
            ->add('count', NumberType::class, [
                'data'  => $datas['count'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'datas' => null
            // Configure your form options here
        ]);
    }
}
