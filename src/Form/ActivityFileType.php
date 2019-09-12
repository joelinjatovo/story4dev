<?php

namespace App\Form;

use App\Entity\ActivityFile;
use App\Entity\Activity;
use App\Entity\File;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('file', EntityType::class, [
                'class' => File::class,
                'choice_label' => function ($file) {
                    return $file->getName();
                }
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => function ($activity) {
                    return $activity->getTitle();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ActivityFile::class,
        ]);
    }
}
