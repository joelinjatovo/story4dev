<?php

namespace App\Form;

use App\Entity\ReportFile;
use App\Entity\File;
use App\Entity\Report;
use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => function ($activity) {
                    return $activity->getTitle();
                }
            ])
            ->add('file', EntityType::class, [
                'class' => File::class,
                'choice_label' => function ($file) {
                    return $file->getName();
                }
            ])
            ->add('report', EntityType::class, [
                'class' => Report::class,
                'choice_label' => function ($report) {
                    return $report->getTitle();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReportFile::class,
        ]);
    }
}
