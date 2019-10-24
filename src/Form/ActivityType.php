<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Project;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', CKEditorType::class, [
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ])
            ->add('contactemail')
            ->add('contactphone')
            ->add('contactaddress')
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => function ($project) {
                    return $project->getTitle();
                }
            ])
            ->add('activityFiles', CollectionType::class, [
                'entry_type' => ActivityFileType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
            'attr'  => [
                'step' => 0.01,
                'min'  => 0,
                'max'  => 1000000000000,
            ]
        ]);
    }
}
