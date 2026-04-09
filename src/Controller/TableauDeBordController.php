<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class TableauDeBordController extends AbstractController
{
    #[Route('/tableau/de/bord', name: 'app_tableau_de_bord')]
    public function index(TaskRepository $taskRepo, ProjectRepository $projectRepo): Response
    {
        $user = $this->getUser();

        // Récupère les projets de l'utilisateur connecté
        $projects = $projectRepo->findBy(['user' => $user]);

        // Récupère toutes les tâches liées aux projets de l'utilisateur
        $tasks = $taskRepo->createQueryBuilder('t')
            ->join('t.project', 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        // Calcul des statistiques
        $stats = [
            'total'    => \count($tasks),
            'a_faire'  => \count(array_filter($tasks, fn($t) => $t->getStatus() === 'a_faire')),
            'en_cours' => \count(array_filter($tasks, fn($t) => $t->getStatus() === 'en_cours')),
            'terminee' => \count(array_filter($tasks, fn($t) => $t->getStatus() === 'terminee')),
        ];

        // Tâches d'aujourd'hui : dueDate = aujourd'hui OU pas de dueDate créée aujourd'hui
        $today = new \DateTime('today');
        $todayTasks = array_values(array_filter($tasks, function (Task $t) use ($today) {
            if ($t->getDueDate()) {
                return $t->getDueDate()->format('Y-m-d') === $today->format('Y-m-d');
            }
            return $t->getCreatedAt()->format('Y-m-d') === $today->format('Y-m-d');
        }));

        return $this->render('tableau_de_bord/index.html.twig', [
            'projects'   => $projects,
            'tasks'      => $tasks,       // toutes les tâches
            'todayTasks' => $todayTasks,  // tâches du jour
            'stats'      => $stats,
        ]);
    }

    #[Route('/projet/creer', name: 'app_projet_creer', methods: ['POST'])]
    public function creerProjet(Request $request, EntityManagerInterface $em): Response
    {
        $nom = trim($request->request->get('name', ''));
        $couleur = $request->request->get('color', '#3B82F6');

        if ($nom !== '') {
            $project = new Project();
            $project->setName($nom);
            $project->setColor($couleur);
            $project->setUser($this->getUser());
            $em->persist($project);
            $em->flush();
        }

        return $this->redirectToRoute('app_tableau_de_bord');
    }

    #[Route('/tache/creer', name: 'app_tache_creer', methods: ['POST'])]
    public function creerTache(Request $request, EntityManagerInterface $em, ProjectRepository $projectRepo): Response
    {
        $task = new Task();
        $task->setTitle($request->request->get('title'));
        $task->setDescription($request->request->get('description'));
        $task->setStatus($request->request->get('status', 'a_faire'));
        $task->setPriority($request->request->get('priority', 'moyenne'));

        $dueDateStr = $request->request->get('dueDate');
        if ($dueDateStr) {
            $task->setDueDate(new \DateTime($dueDateStr));
        }

        $project = $projectRepo->find($request->request->get('project_id'));
        // Comparaison par ID pour éviter les problèmes de proxy Doctrine
        if ($project && $project->getUser()->getId() === $this->getUser()->getId()) {
            $task->setProject($project);
            $em->persist($task);
            $em->flush();
        }

        return $this->redirectToRoute('app_tableau_de_bord');
    }

    #[Route('/tache/supprimer/{id}', name: 'app_tache_supprimer', methods: ['POST'])]
    public function supprimerTache(Task $task, EntityManagerInterface $em): Response
    {
        if ($task->getProject()->getUser()->getId() === $this->getUser()->getId()) {
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('app_tableau_de_bord');
    }

    #[Route('/tache/statut/{id}', name: 'app_tache_statut', methods: ['POST'])]
    public function changerStatut(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        if ($task->getProject()->getUser()->getId() === $this->getUser()->getId()) {
            $task->setStatus($request->request->get('status', 'terminee'));
            $em->flush();
        }

        return $this->redirectToRoute('app_tableau_de_bord');
    }
}
