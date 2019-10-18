<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\OptionType;

class GeneralSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $settings = isset($options['settings'])?$options['settings']:null;

        if( is_array( $settings ) ) {
            foreach($settings as $key => $setting){
                $builder->add($key, OptionType::class, [
                        'data'  => $setting['data'],
                        'label' => $setting['label'],
                        'type'  => isset($setting['type'])?$setting['type']:null,
                        'help'  => isset($setting['help'])?$setting['help']:null,
                        'group'  => isset($setting['group'])?$setting['group']:null
                    ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'settings' => null,
        ]);
    }
}
