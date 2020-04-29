<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

use App\Entity\User;

class AccountProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true, 
            ])
            ->add('fullname', null, ['label' => 'form.label.fullname'])
            ->add('company', null, ['label' => 'form.label.company'])
            ->add('phone', null, ['label' => 'form.label.phone'])
            ->add('website', null, ['label' => 'form.label.website'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
