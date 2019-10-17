<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use \Symfony\Component\Validator\Constraints\NotBlank;
use \Symfony\Component\Validator\Constraints\Length;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required'   => true,
                 'constraints' => [
                     new Length([
                         'max' => 100,
                     ])
                 ]
            ])
            ->add('phone', TextType::class, [
                'required'   => true,
                 'constraints' => [
                     new Length([
                         'max' => 100,
                     ])
                 ]
            ])
            ->add('email', EmailType::class, [
                'required'   => true,
                 'constraints' => [
                     new Length([
                         'max' => 100,
                     ])
                 ]
            ])
            ->add('message', TextareaType::class, [
                'required'   => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
