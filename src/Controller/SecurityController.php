<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        $session = $request->getSession();

        $error = $session->get(SecurityRequestAttributes::AUTHENTICATION_ERROR);
        $lastUsername = $session->get(SecurityRequestAttributes::LAST_USERNAME);

        // On nettoie l’erreur pour éviter qu’elle reste affichée
        $session->remove(SecurityRequestAttributes::AUTHENTICATION_ERROR);

        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}
}