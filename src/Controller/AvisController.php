<?php

namespace App\Controller;

use App\Document\Avis;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis')]
    public function index(): Response
    {
        return $this->render('avis/index.html.twig');
    }

    #[Route('/avis/soumettre', name: 'app_avis_soumettre', methods: ['POST'])]
    public function soumettre(Request $request, DocumentManager $dm): Response
    {
        $nom         = trim($request->request->get('nom', ''));
        $note        = (int) $request->request->get('note', 5);
        $commentaire = trim($request->request->get('commentaire', ''));

        if ($nom !== '' && $commentaire !== '' && $note >= 1 && $note <= 5) {
            $avis = new Avis($nom, $note, $commentaire);
            $dm->persist($avis);
            $dm->flush();
        }

        return $this->redirectToRoute('app_accueil');
    }
}
