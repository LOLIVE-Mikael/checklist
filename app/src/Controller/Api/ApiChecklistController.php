<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChecklistRepository;
use App\Repository\TacheRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Attribute\Route;

class ApiChecklistController extends AbstractController
{
	
	private $entityManager;
	private $checklistRepository;

    public function __construct(EntityManagerInterface $entityManager, ChecklistRepository $checklistRepository, TacheRepository $tacheRepository)
    {
        $this->entityManager = $entityManager;
        $this->checklistRepository = $checklistRepository;
        $this->tacheRepository = $tacheRepository;		
		
    }
	
    #[Route('/checklists/{checklistid}/taches/{tacheid}', methods: ['DELETE'])]
    public function __invoke(int $checklistid, int $tacheid): Response
    {
		// Récupérer la checklist
        $checklist = $this->checklistRepository->find($checklistid);

        // Vérifier si la checklist existe
        if (!$checklist) {
            return new JsonResponse(['message' => 'checklist inexistante'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer la tâche
        $tache = $this->tacheRepository->find($tacheid);

        // Vérifier si la tâche existe
        if (!$tache) {
            return new JsonResponse(['message' => 'tache inexistante'], Response::HTTP_NOT_FOUND);
        }

        // Retirer la tâche de la checklist
        $checklist->removeTache($tache);

        // Mettre à jour la base de données
        $this->entityManager->flush();

		
        return new JsonResponse(['message' => 'Tache retirée'], Response::HTTP_OK); 
    }
}
