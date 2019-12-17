<?php

namespace App\Controller\Web\Security;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google", name="connect_google_start")
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    /**
     * Google redirects to back here afterwards
     *
     * @Route("/connect/google/check", name="connect_google_check")
     */
    public function connectCheck(Request $request)
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Authentificaton non términée. Veuillez réessayer, s\'il vous plaît');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->redirectToRoute('app_index');
        }

    }

}