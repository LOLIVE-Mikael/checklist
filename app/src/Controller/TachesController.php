<?php

namespace App\Controller;

use App\Entity\Taches;
use App\Form\TachesType;
use App\Repository\TachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class TachesController extends AbstractController
{
    #[Route('/taches', name: 'taches_dashboard')]
    public function index(TachesRepository $tachesRepository): Response
    {
		$tasks = $tachesRepository->findAll();
        $forms = [];
        foreach ($tasks as $task) {
			$form = $this->createForm(TachesType::class, $task)				
				->add('save', SubmitType::class, ['label' => 'Modifier'])
				->add('delete', SubmitType::class, ['label' => 'Supprimer']);
			$forms[$task->getId()] = $form->createView();
        }

		// Créer un formulaire pour créer une nouvelle tâche
		$newTask = new Taches();
		$newTaskForm = $this->createForm(TachesType::class, $newTask)
			->add('save', SubmitType::class, ['label' => 'Créer'])
			->createView();


        return $this->render('taches/index.html.twig', [
            'forms' => $forms,
			'newTaskForm' => $newTaskForm,			
        ]);
    }
	
	#[Route('/taches/modifier/{taskId}', name: 'taches_modification')]
    public function update(int $taskId, TachesRepository $tachesRepository, EntityManagerInterface $entityManager, Request $request): Response{

		// Récupérer la tâche à modifier à partir de l'ID
		$task = $tachesRepository->find($taskId);

	
		if (!$task) {
			// Gérer le cas où la tâche n'est pas trouvée
			throw $this->createNotFoundException('La tâche n\'existe pas');
		}
		$form = $this->createForm(TachesType::class, $task)				
			->add('save', SubmitType::class, ['label' => 'Modifier'])
			->add('delete', SubmitType::class, ['label' => 'Supprimer']);
		$form->handleRequest($request);
		
		//DUMP($form->getClickedButton()->getName());die();
		

		if ($form->isSubmitted() && $form->isValid()) {

			if ($form->getClickedButton()->getName() == 'delete'){
				$entityManager->remove($task);				
				$entityManager->flush();
			} else {
				var_dump($task->getTitre());
				$entityManager->persist($task);
				$entityManager->flush();
			}
		}
		// Rediriger vers une autre page après la création de la tâche
		return $this->redirectToRoute('taches_dashboard');
	}

	#[Route('/taches/creer', name: 'taches_creation')]
    public function create(TachesRepository $tachesRepository, EntityManagerInterface $entityManager, Request $request): Response{		
		$task = new Taches();
		$form = $this->createForm(TachesType::class, $task);
	    $form->add('save', SubmitType::class, ['label' => 'Créer']);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($task);
			$entityManager->flush();
		}
		// Rediriger vers une autre page après la création de la tâche
		return $this->redirectToRoute('taches_dashboard');

	}

}
