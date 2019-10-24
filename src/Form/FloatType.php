<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FloatType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'scale' => 2,
            'attr'  => [
                'step' => 0.01,
                'min'  => 0,
                'max'  => 9000000000000,
            ]
        ]);
    }
    
    public function getParent()
    {
        return NumberType::class;
    }

    public function getName()
    {
        return 'float';
    }
}
