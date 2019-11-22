<?php

namespace App\Form;

use App\Entity\Axe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AxeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = isset($options['project'])?$options['project']:null;
        
        $builder
            ->add('id', HiddenType::class)
            ->add('title')
            ->add('startAt', DateType::class, [
                'widget'     => 'single_text',
                'html5'      => false,
            ])
            ->add('endAt', DateType::class, [
                'widget'     => 'single_text',
                'html5'      => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Axe::class,
            'project'    => null,
        ]);
    }
}
