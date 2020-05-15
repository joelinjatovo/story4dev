<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\Periodicity;
use App\Repository\ProjectRepository;
use App\Form\FloatType;
use App\Form\AddressType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slug', null, ['label' => 'form.label.project.slug'])
            ->add('title', null, ['label' => 'form.label.project.title'])
            ->add('pictureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true, 
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'form.label.project.description',
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ])
            ->add('budget', NumberType::class, [
                'label' => 'form.label.project.budget',
                'required'   => false,
            ])
            ->add('currency', CurrencyType::class, [
                'label' => 'form.label.project.currency',
                'required'   => false,
            ])
            ->add('start_at', DateType::class, [
                'label' => 'form.label.project.start_at',
                'widget'     => 'single_text',
                'html5'      => false,
            ])
            ->add('end_at', DateType::class, [
                'label' => 'form.label.project.end_at',
                'widget'     => 'single_text',
                'html5'      => false,
            ])
            ->add('contactemail', null, ['label' => 'form.label.contact.email'])
            ->add('contactphone', null, ['label' => 'form.label.contact.phone'])
            ->add('contactaddress', null, ['label' => 'form.label.contact.address'])
            //->add('address', AddressType::class)
            ->add('periodicity', EntityType::class, [
                'label' => 'form.label.project.periodicity',
                'class' => Periodicity::class,
                'choice_label' => function ($periodicty) {
                    return $periodicty->getTitle();
                }
            ])
            ->add('iterations', CollectionType::class, [
                'label' => 'form.label.project.iterations',
                'entry_type' => IterationType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('author', EntityType::class, [
                'label' => 'form.label.project.author',
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getFullName();
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'button.save'
            ])
        ;
        
        $fields = isset($options['fields'])?$options['fields']:null;
        if( is_array( $fields ) ) {
            foreach($fields as $group => $metas){
                $type = $metas['form_type'];
                unset($metas['form_type']);
                
                $builder->add($group, CollectionType::class, [
                    'entry_type' => $type,
                    'entry_options' => [
                        //'label' => false
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'mapped' => false,
                    'data' => $metas['data'],
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'fields'     => null,
        ]);
    }
}
