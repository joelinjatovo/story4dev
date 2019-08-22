<?php

namespace App\Form;

use App\Entity\Result;
use App\Entity\Indicator;
use App\Entity\Project;
use App\Repository\IndicatorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = isset($options['project'])?$options['project']:null;
        
        $builder
            ->add('value', FloatType::class, [
                'label_attr' => ['class' => 'col-form-label'],
                'attr' => ['class' => 'form-control'],
                'required'   => true,
            ])
            ->add('indicator', EntityType::class, [
                'placeholder' => 'Choose an indicator',
                'required' => true,
                'class' => Indicator::class,
                'query_builder' => function (IndicatorRepository $er) use ($project) {
                    
                    if(! $project){
                        return $er->createQueryBuilder('i')
                            ->orderBy('i.title', 'ASC');
                    }
                    
                    return $er->createQueryBuilder('i')
                        ->innerJoin('i.activity', 'a')
                        ->where('a.project = :project')
                        ->setParameter('project', $project)
                        ->orderBy('i.title', 'ASC');
                    
                },
                'choice_label' => function ($indicator) {
                    return $indicator->getTitle();
                },
                'group_by' => function($indicator, $key, $value) {
                    return $indicator->getActivity()->getTitle();
                },
            ])
            /*
            ->add('title')
            ->add('description')
            ->add('author')
            ->add('report')
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Result::class,
            'project'    => null,
            'author'     => null,
        ]);
    }
}
