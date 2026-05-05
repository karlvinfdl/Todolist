<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/conditions-utilisation', name: 'app_conditions')]
    public function conditions(): Response
    {
        return $this->render('legal/conditions.html.twig');
    }

    #[Route('/politique-confidentialite', name: 'app_politique_confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('legal/politique-confidentialite.html.twig');
    }

    #[Route('/politique-cookies', name: 'app_cookies')]
    public function cookies(): Response
    {
        return $this->render('legal/cookies.html.twig');
    }
}
