<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Project;
use App\Repository\ActivityRepository;
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
        
        $project = isset($options['project'])?$options['project']:null;
        
        $entity = $builder->getData();
        
        $builder
            ->add('title', null, ['label' => 'form.label.title.activity'])
            ->add('description', CKEditorType::class, [
                'label' => 'form.label.description.activity',
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ])
            ->add('contactemail', null, ['label' => 'form.label.email.contact'])
            ->add('contactphone', null, ['label' => 'form.label.phone.contact'])
            ->add('contactaddress', null, ['label' => 'form.label.address.contact'])
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
                'label' => 'button.save.activity'
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
            ],
            'project' => null,
        ]);
    }
}
