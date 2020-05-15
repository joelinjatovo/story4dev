<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Report;
use App\Entity\Project;
use App\Form\ResultType;
use App\Form\ReportFileType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $activity = isset($options['activity'])?$options['activity']:null;
        $user = isset($options['user'])?$options['user']:null;
        $favorites = isset($options['favorites'])?$options['favorites']:[];
        
        $builder
            ->add('title', null, ['label' => 'form.label.report.title'])
            ->add('description', CKEditorType::class, [
                'label' => 'form.label.report.description',
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ])
            ->add('longitude', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('altitude', HiddenType::class)
            ->add('results', CollectionType::class, [
                'label' => 'form.label.report.results',
                'entry_type' => ResultType::class,
                'entry_options' => [
                    'activity' => $activity,
                    'user' => $user,
                    'favorites' => $favorites,
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('reportFiles', CollectionType::class, [
                'entry_type' => ReportFileType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('activity', EntityType::class, [
                'label' => 'form.label.report.activity',
                'class' => Activity::class,
                'choice_label' => function ($activity) {
                    return $activity->getTitle();
                }
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'form.label.report.createdAt',
                'widget'     => 'single_text',
                'html5'      => false,
            ])
            ->add('publishExternally', CheckboxType::class, [
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'form.label.report.status',
                'choices'  => [
                    'status.opened' => Report::STATUS_OPENED,
                    'status.closed'  => Report::STATUS_CLOSED,
                    'status.terminated'  => Report::STATUS_TERMINATED,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
            'activity' => null,
            'user' => null,
            'favorites' => [],
        ]);
    }
}
