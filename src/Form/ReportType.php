<?php

namespace App\Form;

use App\Entity\Report;
use App\Entity\Project;
use App\Form\ResultType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
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
            ->add('results', CollectionType::class, [
                'entry_type' => ResultType::class,
                'entry_options' => [
                    'project' => isset($options['project'])?$options['project']:null,
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => function ($project) {
                    return $project->getTitle();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
            'project'    => null,
            'author'     => null,
        ]);
    }
}
