<?php

namespace App\Helper;

use App\Service\OptionService;

class MessageHelper {
    
    public static function getMessage(OptionService $optionService, string $subject){
        $email = $optionService->get('app_admin_email')??'admin@story4dev.com';
        $name  = $optionService->get('app_admin_name')??'Admin';
        $admins = [
            $email => $name,
        ];
        $subject .= ' - ['. $optionService->get('app_name') . ']';

        return (new \Swift_Message($subject))
        ->setFrom($admins);
    }
    
}