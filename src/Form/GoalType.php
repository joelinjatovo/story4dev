<?php

namespace App\Form;

use App\Entity\Goal;
use App\Entity\Indicator;
use App\Entity\Iteration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GoalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', NumberType::class)
            ->add('indicator', EntityType::class, [
                'class' => Indicator::class,
                'choice_label' => function ($indicator) {
                    return $indicator->getTitle();
                }
            ])
            ->add('iteration', EntityType::class, [
                'class' => Iteration::class,
                'choice_label' => function ($iteration) {
                    return $iteration->getTitle();
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save goal'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Goal::class,
        ]);
    }
}
