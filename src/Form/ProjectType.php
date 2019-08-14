<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Periodicity;
use App\Form\FloatType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
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
            ->add('budget', FloatType::class, [
                'required'   => false,
            ])
            ->add('start_at', DateTimeType::class, [
                'date_label'  => 'Starts On',
            ])
            ->add('end_at', DateTimeType::class, [
                'date_label' => 'Ends At',
            ])
            ->add('periodicity', EntityType::class, [
                'class' => Periodicity::class,
                'choice_label' => function ($periodicty) {
                    return $periodicty->getTitle();
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save project'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
