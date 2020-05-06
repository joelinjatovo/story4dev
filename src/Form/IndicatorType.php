<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Indicator;
use App\Entity\Unit;
use App\Repository\ActivityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class IndicatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $project = isset($options['project'])?$options['project']:null;
        
        $builder
            ->add('title')
            ->add('activity', EntityType::class, [
                'label' => 'form.label.indicator.activity',
                'placeholder' => 'form.placeholder.activity',
                'class' => Activity::class,
                'query_builder' => function (ActivityRepository $er) use ($project) {
                    $query = $er->createQueryBuilder('a');
                    
                    if( $project ) {
                        $query->andWhere('a.project = :project')
                            ->setParameter('project', $project);
                    }
                    
                    return $query->distinct('a.id')
                        ->orderBy('a.title', 'ASC');
                    
                },
                'choice_label' => function ($activity) {
                    return $activity->getTitle();
                }
            ])
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => function ($unit) {
                    return $unit->getTitle();
                }
            ])
            ->add('goals', CollectionType::class, [
                'entry_type' => GoalType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('cummulative', CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save indicator'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Indicator::class,
            'project'    => null,
        ]);
    }
}
