<?php

namespace App\Helper;

use App\Service\OptionService;

class MessageHelper {
    
    public static function getMessage(OptionService $optionService, string $subject){
        $admins = [
            $optionService->get('app_admin_email') => $optionService->get('app_admin_name'),
        ];
        $subject .= ' - ['. $optionService->get('app_name') . ']';

        return (new \Swift_Message($subject))
        ->setFrom($admins);
    }
    
}