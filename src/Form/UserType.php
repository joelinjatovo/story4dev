<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar')
            ->add('username')
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('slug')
            ->add('fullname')
            ->add('title')
            ->add('presentation')
            ->add('phone')
            ->add('status')
            ->add('isActive')
            ->add('isVerified')
            ->add('confirmToken')
            ->add('confirmedAt')
            ->add('resetToken')
            ->add('resetedAt')
            ->add('activedAt')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('address')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
