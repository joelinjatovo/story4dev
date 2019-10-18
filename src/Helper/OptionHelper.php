<?php

namespace App\Helper;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use App\Entity\Option;

class OptionHelper {
    
    public static function getOptionsFields($em){
        $keys = [
            'app_name' => [
                'label' => "Nom de l'application",
                'group' => [
                    'id' => '__row_group_app',
                    'label' => 'Application',
                ]
            ],
            'app_admin_name' => [
                'label' => "Nom de l'admin"
            ],
            'app_admin_email' => [
                'label' => "Email de l'admin",
                'type'  => EmailType::class
            ],
            'seo_title' => [
                'label' => "Meta Titre",
                'group' => [
                    'id' => '__row_group_seo',
                    'label' => 'SEO',
                ]
            ],
            'seo_description' => [
                'label' => "Meta Description",
                'type'  => TextareaType::class
            ],
            'seo_keywords' => [
                'label' => "Meta Keywords",
                'type'  => TextareaType::class
            ],
        ];
        
        $repository = $em->getRepository(Option::class);

        $datas = [];
        $options = $repository->findAll(['autoload' => 1]);
        foreach($options as $option){
            $datas[$option->getOptionKey()] = $option;
        }

        $settings = [];

        foreach($keys as $key => $value){
            $setting = isset($datas[$key]) ? $datas[$key] : null;

            if( ! $setting ){
                $setting = new Option();
                $setting->setOptionKey($key);
            }

            $settings[$key] = [
                'data'  => $setting,
                'label' => $value['label'],
                'type'  => isset( $value['type'] ) ? $value['type'] : null,
                'group'  => isset( $value['group'] ) ? $value['group'] : null
            ];
        }

        return $settings;
    }
    
}