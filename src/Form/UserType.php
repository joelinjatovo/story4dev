<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true, 
            ])
            ->add('username', null, ['label' => 'form.label.username'])
            ->add('email', null, ['label' => 'form.label.email'])
            ->add('fullname', null, ['label' => 'form.label.fullname'])
            ->add('company', null, ['label' => 'form.label.company'])
            ->add('phone', null, ['label' => 'form.label.phone'])
            ->add('website', null, ['label' => 'form.label.website'])
            ->add('isVerified', null, ['label' => 'form.label.verified'])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                     'form.label.status.ping' => User::STATUS_PING,
                     'form.label.status.active' => User::STATUS_ACTIVE,
                     'form.label.status.blocked' => User::STATUS_BLOCKED,
                     'form.label.status.canceled' => User::STATUS_CANCELED,
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true, // render check-boxes
                'choices'  => [
                    'form.label.role.customer' => 'ROLE_USER',
                    'form.label.role.admin' => 'ROLE_ADMIN',
                    'form.label.role.superadmin' => 'ROLE_SUPER_ADMIN',
                ],
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
