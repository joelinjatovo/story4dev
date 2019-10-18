<?php

namespace App\Form;

use App\Entity\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class OptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = isset($options['type'])?$options['type']:null;
        $group = isset($options['group'])?$options['group']:null;

        if($group){
            $builder->add('group', HiddenType::class, ['mapped' => false, 'required' => false ]);
        }

        $builder
            ->add('id', HiddenType::class)
            ->add('option_key', HiddenType::class);
        
        if( $type != null ){
            $builder
                ->add('option_value', $type);
        }else{
            $builder
                ->add('option_value');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Option::class,
            'type' => null,
            'group' => null
        ]);
    }
}
