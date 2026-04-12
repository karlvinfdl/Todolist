<?php

namespace App\Controller;

use App\Document\Avis;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(
        UserRepository $userRepo,
        ProjectRepository $projectRepo,
        TaskRepository $taskRepo,
        DocumentManager $dm,
    ): Response {
        $users    = $userRepo->findAll();
        $projects = $projectRepo->findAll();
        $tasks    = $taskRepo->findAll();
        $avis     = $dm->getRepository(Avis::class)->findAll();

        $stats = [
            'users'    => \count($users),
            'projects' => \count($projects),
            'tasks'    => \count($tasks),
            'avis'     => \count($avis),
            'a_faire'  => \count(array_filter($tasks, fn($t) => $t->getStatus() === 'a_faire')),
            'en_cours' => \count(array_filter($tasks, fn($t) => $t->getStatus() === 'en_cours')),
            'terminee' => \count(array_filter($tasks, fn($t) => $t->getStatus() === 'terminee')),
        ];

        return $this->render('admin/index.html.twig', [
            'users'    => $users,
            'projects' => $projects,
            'avis'     => $avis,
            'stats'    => $stats,
        ]);
    }

    #[Route('/utilisateur/supprimer/{id}', name: 'app_admin_supprimer_user', methods: ['POST'])]
    public function supprimerUser(User $user, EntityManagerInterface $em): Response
    {
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin');
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/utilisateur/role/{id}', name: 'app_admin_role_user', methods: ['POST'])]
    public function toggleRole(User $user, EntityManagerInterface $em): Response
    {
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('app_admin');
        }

        $roles = $user->getRoles();

        if (\in_array('ROLE_ADMIN', $roles, true)) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $em->flush();

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/avis/supprimer/{id}', name: 'app_admin_supprimer_avis', methods: ['POST'])]
    public function supprimerAvis(string $id, DocumentManager $dm): Response
    {
        $avis = $dm->getRepository(Avis::class)->find($id);

        if ($avis) {
            $dm->remove($avis);
            $dm->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/avis/valider/{id}', name: 'app_admin_valider_avis', methods: ['POST'])]
    public function validerAvis(string $id, DocumentManager $dm): Response
    {
        $avis = $dm->getRepository(Avis::class)->find($id);

        if ($avis) {
            $avis->setValide(!$avis->isValide());
            $dm->flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}
