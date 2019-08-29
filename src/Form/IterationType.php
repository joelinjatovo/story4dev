<?php

namespace App\Form;

use App\Entity\Iteration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IterationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = isset($options['project'])?$options['project']:null;
        
        $builder
            ->add('title')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Iteration::class,
            'project'    => null,
        ]);
    }
}
