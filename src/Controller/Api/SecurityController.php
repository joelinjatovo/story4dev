<?php

namespace App\Controller\Api;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SecurityController extends AbstractController
{
    /**
     * @Route("/token", name="api_token", methods="POST")
     */
    public function token(Request $request)
    {
        //return new JsonResponse(['token' => $request->getUser()]);
        
        $user = $this->getDoctrine()
            ->getRepository(\App\Entity\User::class)
            ->findOneBy(['username' => $request->getUser()]);
        
        if (!$user) {
            throw $this->createNotFoundException();
        }
        
        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $request->getPassword());
        
        if (!$isValid) {
            throw new BadCredentialsException();
        }
        
        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 3600 // 1 hour expiration
            ]);
        
        return new JsonResponse(['token' => $token]);
        
        /*
        $token = new \App\Entity\Token();
        $token->setUser($user);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($token);
        $entityManager->flush();
        
        return new JsonResponse(['token' => $token->getToken()]);
        */

    }
    /**
     *
     * @Route("/login/check", name="api_login_check")
     */
    public function loginCheck(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse(array('status' => false, 'message' => "User not found!"));
        } else {
            return new JsonResponse(array('status' => $this->getUser()->getTokens()));
        }

    }

}