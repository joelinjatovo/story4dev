<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Project;
use App\Form\FloatType;
use App\Form\AddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('budget', FloatType::class, [
                'required'   => false,
            ])
            ->add('contact_email')
            ->add('contact_phone')
            ->add('contact_address')
            ->add('address', AddressType::class)
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => function ($project) {
                    return $project->getTitle();
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save activity'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
