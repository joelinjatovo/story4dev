<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true, 
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Upload File'
            ])
            /*
            ->add('username')
            ->add('email')
            ->add('password')
            ->add('fullname')
            ->add('title')
            ->add('presentation')
            ->add('phone')
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
