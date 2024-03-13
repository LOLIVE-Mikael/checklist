<?php

namespace App\Controller;

use App\Entity\Checklists;
use App\Entity\Taches;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChecklistsRepository;
use App\Repository\TachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use App\Service\FormServiceAddTask;

class ChecklistController extends AbstractController
{	

    private $formServiceAddTask;

    public function __construct(FormServiceAddTask $formServiceAddTask)
    {
        $this->formServiceAddTask = $formServiceAddTask;
    }

	private function createFormSelectionChecklist (ChecklistsRepository $checklistsRepository, Checklists $checklist=null): Form
	{
		/* chargement des données de la checklist. parfois nécessaire pour la présélection dans la liste */
		if ($checklist) {
			$checklist->getTaches()->initialize();
		}

        // Récupérer toutes les checklists
        $checklists = $checklistsRepository->findAll();

		$form = $this->createFormBuilder()
            ->add('checklist', ChoiceType::class, [
                'choices' => $checklists,
                'choice_label' => 'titre',
				'choice_value' => 'id',
   	            'placeholder' => 'Choisir une checklist',
				'data' => $checklist, 
				'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Voir',
            ])
            ->getForm();
			
			
		return $form;
	}
	
    #[Route('/checklist/{checklistId?}', name: 'checklist_dashboard', requirements: ['checklistId' => '\d+'])]
    public function index(?int $checklistId, Request $request, ChecklistsRepository $checklistsRepository, TachesRepository $tachesRepository): Response
    {	
		if($checklistId){
			$selectedChecklist = $checklistsRepository->find($checklistId);
		} else {
			$selectedChecklist=NULL;
		}
		
        // Créer le formulaire de sélection de la checklist
        $form = $this->createFormSelectionChecklist($checklistsRepository,$selectedChecklist);
        if ($selectedChecklist) {
            // Récupérer les tâches de la checklist sélectionnée
			$tasks = $selectedChecklist->getTaches();
			$formAjout = $this->formServiceAddTask->createAjoutTacheForm($selectedChecklist);

			// Afficher le formulaire dans le template Twig
			return $this->render('checklist/index.html.twig', [
				'form' => $form->createView(),
				'formajout' => $formAjout->createView(),
				'tasks' => $tasks, 
				'checklist' => $selectedChecklist,
			]);
        }
        // Afficher le formulaire dans le template Twig
		return $this->render('checklist/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/checklist/selection', name: 'checklist_selection_checklist')]
    public function readChecklist(Request $request,ChecklistsRepository $checklistsRepository): Response
    {
		$form = $this->createFormSelectionChecklist($checklistsRepository);
		$form->handleRequest($request);

		$checklist = $form->get('checklist')->getData();
			if($checklist){
				$checklistId = $checklist->getId();
			} else {
				$checklistId = null;
			}
		return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $checklistId]);
	}
	
    #[Route('/checklist/addtache', name: 'checklist_add_task')]	
	public function handleAddTask(EntityManagerInterface $entityManager, Request $request, ChecklistsRepository $checklistsRepository, TachesRepository $tachesRepository)
    {
		// Récupérer les données soumises par le formulaire
		$formAjout = $this->formServiceAddTask->createAjoutTacheForm();		
		$formAjout->handleRequest($request);
				
		$task = $formAjout->get('task')->getData();
		$newTitleTask = $formAjout->get('newTask')->getData();
		
        $selectedChecklist = $checklistsRepository->find($formAjout->get('checklist_id')->getData());
		$selectedChecklist->getTaches()->initialize();	
	
        if (!$task && $newTitleTask) {
            // Créer une nouvelle instance de Taches
            $newTask = new Taches();
            $newTask->setTitre($newTitleTask);
            // Associer la nouvelle tâche à la checklist
            $selectedChecklist->addTache($newTask);

            // Persistez et flush la nouvelle tâche
            $entityManager->persist($newTask);
			$entityManager->flush();
			
			return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $selectedChecklist->getId()]);	
        
		} elseif ($task) {
            // Ajouter la tâche sélectionnée à la checklist
			$task->getChecklists()->initialize();		
            $selectedChecklist->addTache($task);

			$entityManager->persist($selectedChecklist);
			$entityManager->flush();
			$this->addFlash('success', 'La nouvelle tâche a été ajoutée avec succès !');
			
			return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $selectedChecklist->getId()]);	

        } else {
		// En cas de soumission invalide, ou si la requête n'est pas de type POST,
		// rediriger vers une page d'erreur ou afficher un message d'erreur
		return new Response('Invalid form submission', Response::HTTP_BAD_REQUEST);  
		}
	}
	
	#[Route('/checklist/removetask/{taskId}/{checklistId}', name: 'checklist_remove_task')]
	public function removeTask(int $taskId, int $checklistId, EntityManagerInterface $entityManager, TachesRepository $tachesRepository, ChecklistsRepository $checklistsRepository): Response
	{
		$task = $tachesRepository->find($taskId);
		$checklist = $checklistsRepository->find($checklistId);

		if (!$task || !$checklist) {
			throw $this->createNotFoundException('La tâche ou la checklist n\'a pas été trouvée.');
		}

		// Dissocier la tâche de la checklist
		$checklist->removeTache($task);
		$entityManager->flush();
		
		return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $checklist->getId()]);	
	}
	
	
	
}
