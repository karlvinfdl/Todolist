<?php

namespace App\Controller;

use App\Document\Avis;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(DocumentManager $dm): Response
    {
        $avis = $dm->getRepository(Avis::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('accueil/index.html.twig', [
            'avis' => $avis,
        ]);
    }
}
