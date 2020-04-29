<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\User;

class AccountInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, ['label' => 'form.label.username'])
            ->add('email', null, ['label' => 'form.label.email'])
            ->add('language', ChoiceType::class, [
                'label' => 'form.label.language',
                'choices' => [
                    'English'   => 'en',
                    'Français'  => 'fr',
                ],
                'required' => true,
                'placeholder' => 'form.placeholder.language',
                //'preferred_choices' => ['fr'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
