<?php

namespace App\Helper;

use App\Entity\Option;

class OptionHelper {
    
    public static function getOptionsFields(){
        $keys = [
            'app' => [
                'app_name',
                'app_admin_name',
                'app_admin_email',
            ],
            'seo' => [
                'seo_title',
                'seo_keywords',
                'seo_description',
            ],
        ];
    }
    
}