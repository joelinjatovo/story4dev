<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\User;

class AccountPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'form.label.password.old',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ]
            ])
            ->add('newpassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'form.label.password.validation',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'form.label.password.new'],
                'second_options' => ['label' => 'form.label.password.confirm'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => User::class,
        ]);
    }
}
