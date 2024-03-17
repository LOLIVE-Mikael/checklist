<?php

namespace App\Controller;

use App\Entity\Taches;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ApiTachesController extends AbstractController
{
	  /**
     * @Route("/apitaches", name="api_create_tache", methods={"POST"})
     * 
     * @SWG\Post(
     *     tags={"Taches"},
     *     summary="Créer une nouvelle tâche",
     *     description="Créer une nouvelle tâche à partir des données fournies.",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Données de la tâche à créer",
     *         required=true,
     *         @SWG\Schema(ref=@Model(type=Taches::class, groups={"minimal"}))
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Tâche créée avec succès",
     *         @Model(type=Taches::class, groups={"minimal"})
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Requête invalide"
     *     ),
     *     security={
     *         {"Bearer": {}}
     *     }
     * )
     */
    #[Route('/apitaches', name: 'api_create_tache', methods: ['POST'])]
    public function createTache(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer): JsonResponse
    {
		
		
		try {
			// Récupérer les données de la requête
			$data = json_decode($request->getContent(), true);
			if (empty($data)) {
				throw new \Exception('La requête est vide. Veuillez fournir des données valides.', 400);
			}

			// Créer une nouvelle instance de l'entité Taches
			$tache = new Taches();

			// Définir les valeurs de la tâche
			$tache->setTitre($data['titre']); // Assurez-vous que 'titre' correspond à la clé dans les données JSON

			// Enregistrer la tâche dans la base de données
			$entityManager->persist($tache);
			$entityManager->flush();

			// Répondre avec les données de la tâche créée
			$response = new JsonResponse(
				$serializer->serialize($tache, 'json', ['groups' => 'minimal']),
				JsonResponse::HTTP_CREATED,
				[],
				true
			);

		} catch (\Exception $e) {
			$errorMessage = $e->getMessage();
			$statusCode = $e->getCode() ?: JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
			$response = new JsonResponse(['error' => $errorMessage], $statusCode);
		}
		return $response;
    }
}
