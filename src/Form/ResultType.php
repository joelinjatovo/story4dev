<?php

namespace App\Form;

use App\Entity\Result;
use App\Entity\Indicator;
use App\Entity\Project;
use App\Repository\IndicatorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ResultType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = isset($options['project'])?$options['project']:null;
        $user = isset($options['user'])?$options['user']:null;
        $favorites = isset($options['favorites'])?$options['favorites']:[];
        
        $builder
            ->add('id', HiddenType::class)
            ->add('value', NumberType::class, [
                'label_attr' => ['class' => 'col-form-label'],
                'attr' => ['class' => 'form-control'],
                'required'   => true,
            ])
            ->add('indicator', EntityType::class, [
                'placeholder' => 'Sélectionner un indicateur',
                'required' => true,
                'class' => Indicator::class,
                'query_builder' => function (IndicatorRepository $er) use ($project) {
                    if( ! $project ) {
                        return $er->createQueryBuilder('i')
                            ->orderBy('i.title', 'ASC');
                    }
                    
                    return $er->createQueryBuilder('i')
                            ->join('i.activity', 'a')
                            ->where('a.project = :project')
                            ->setParameter('project', $project)
                            ->orderBy('i.title', 'ASC');
                    
                },
                'choice_label' => function ($indicator) {
                    return $indicator->getTitle();
                },
                'preferred_choices' => function ($indicator, $key, $value) use ($favorites) {
                    return in_array($indicator->getId(), $favorites);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Result::class,
            'project' => null,
            'user' => null,
            'favorites' => [],
        ]);
    }
}
