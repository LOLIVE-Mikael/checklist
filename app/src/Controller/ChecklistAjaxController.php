<?php

namespace App\Controller;

use App\Entity\Checklists;
use App\Entity\Taches;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ChecklistsRepository;
use App\Repository\TachesRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FormServiceAddTask;

class ChecklistAjaxController extends AbstractController
{
    private $formServiceAddTask;

    public function __construct(FormServiceAddTask $formServiceAddTask)
    {
        $this->formServiceAddTask = $formServiceAddTask;
    }
	
    #[Route('/checklistajax/checklist', name: 'checklist_ajax_data')]
    public function getChecklistData(TachesRepository $tachesRepository, ChecklistsRepository $checklistsRepository, Request $request): Response
    {
		// Récupérer l'ID de la checklist depuis la requête AJAX
		$checklistId = $request->query->get('checklistId');
		if($checklistId){
			$selectedChecklist = $checklistsRepository->find($checklistId);
			$tasks = $selectedChecklist->getTaches();
			$formAjout = $this->formServiceAddTask->createAjoutTacheForm($selectedChecklist)->createView();

		} else {
			$selectedChecklist=NULL;
			$tasks = '';
			$formAjout = NULL;
		}
		
		// Afficher le formulaire dans le template Twig
		return $this->render('checklist/tasks.html.twig', [
			'formajout' => $formAjout,
			'tasks' => $tasks, 
			'checklist' => $selectedChecklist,
		]);			
	}
	
    #[Route('/checklistajax/removetask', name: 'checklist_ajax_remove_task')]
    public function removeTaskAjax(EntityManagerInterface $entityManager, TachesRepository $tachesRepository, ChecklistsRepository $checklistsRepository, Request $request): Response
    {
	    $taskId = $request->request->get('taskId');
	    $checklistId = $request->request->get('checklistId');
		if (!$taskId || !$checklistId) {
	        return new JsonResponse(['error' => 'Tache ou Checklist non envoyée'], 404);
		}
		
		$task = $tachesRepository->find($taskId);
		$checklist = $checklistsRepository->find($checklistId);		
		if (!$task || !$checklist) {
	        return new JsonResponse(['error' => 'Tache ou Checklist non trouvée'], 404);
		}

		// Dissocier la tâche de la checklist
		$checklist->removeTache($task);
		$entityManager->flush();

		return new JsonResponse(['success' => true]);		
	}
	
	#[Route('/checklistajax/addtask', name: 'checklist_ajax_add_task')]
    public function addTaskAjax(EntityManagerInterface $entityManager, TachesRepository $tachesRepository, ChecklistsRepository $checklistsRepository, Request $request): Response
	{
	    $taskId = $request->request->get('taskId');
		if ($taskId){
			$task = $tachesRepository->find($taskId);

		} else {
            // Créer une nouvelle instance de Taches
			$task = new Taches();
			$newTitleTask=$request->request->get('newTask');
            $task->setTitre($newTitleTask);
			// Persistez et flush la nouvelle tâche
            $entityManager->persist($task);
			$entityManager->flush();
		}

	    $checklistId = $request->request->get('checklistId');

        $selectedChecklist = $checklistsRepository->find($checklistId);
		$selectedChecklist->getTaches()->initialize();	

		$task->getChecklists()->initialize();		
        $selectedChecklist->addTache($task);

		$entityManager->persist($selectedChecklist);
		$entityManager->flush();
		
		// Afficher la formulaire dans le template Twig
		return $this->render('checklist/task.html.twig', [
			'task' => $task,
			'checklist' => $selectedChecklist
		]);	
		
	}
}
