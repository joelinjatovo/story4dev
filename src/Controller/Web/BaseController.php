<?php

namespace App\Controller\Web;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function trans($message, $args = [], $domain = 'messages')
    {
        return $this->translator->trans($message, $args, $domain);
    }
}
