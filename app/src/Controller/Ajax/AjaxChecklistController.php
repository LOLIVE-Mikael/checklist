<?php

namespace App\Controller\Ajax;

use App\Entity\Checklist;
use App\Entity\Tache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ChecklistRepository;
use App\Repository\TacheRepository;
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

class AjaxChecklistController extends AbstractController
{
    private $formServiceAddTask;

    public function __construct(FormServiceAddTask $formServiceAddTask)
    {
        $this->formServiceAddTask = $formServiceAddTask;
    }
	
    #[Route('/ajax/checklist', name: 'checklist_ajax_data')]
    public function getChecklistData(TacheRepository $tacheRepository, ChecklistRepository $checklistRepository, Request $request): Response
    {
		// Récupérer l'ID de la checklist depuis la requête AJAX
		$checklistId = $request->query->get('checklistId');
		if($checklistId){
			$selectedChecklist = $checklistRepository->find($checklistId);
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
	
    #[Route('/ajax/checklist/removetask', name: 'checklist_ajax_remove_task')]
    public function removeTaskAjax(EntityManagerInterface $entityManager, TacheRepository $tacheRepository, ChecklistRepository $checklistRepository, Request $request): Response
    {
	    $taskId = $request->request->get('taskId');
	    $checklistId = $request->request->get('checklistId');
		if (!$taskId || !$checklistId) {
	        return new JsonResponse(['error' => 'Tache ou Checklist non envoyée'], 404);
		}
		
		$task = $tacheRepository->find($taskId);
		$checklist = $checklistRepository->find($checklistId);		
		if (!$task || !$checklist) {
	        return new JsonResponse(['error' => 'Tache ou Checklist non trouvée'], 404);
		}

		// Dissocier la tâche de la checklist
		$checklist->removeTache($task);
		$entityManager->flush();

		return new JsonResponse(['success' => true]);		
	}
	
	#[Route('/ajax/checklist/addtask', name: 'checklist_ajax_add_task')]
    public function addTaskAjax(EntityManagerInterface $entityManager, TacheRepository $tacheRepository, ChecklistRepository $checklistRepository, Request $request): Response
	{
	    $taskId = $request->request->get('taskId');
		if ($taskId){
			$task = $tacheRepository->find($taskId);

		} else {
            // Créer une nouvelle instance de Taches
			$task = new Tache();
			$newTitleTask=$request->request->get('newTask');
            $task->setTitre($newTitleTask);
			// Persistez et flush la nouvelle tâche
            $entityManager->persist($task);
			$entityManager->flush();
		}

	    $checklistId = $request->request->get('checklistId');

        $selectedChecklist = $checklistRepository->find($checklistId);
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
