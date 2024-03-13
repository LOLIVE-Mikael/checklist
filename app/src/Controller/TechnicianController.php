<?php

namespace App\Controller;

use App\Entity\Checklists;
use App\Repository\ChecklistsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechnicianController extends AbstractController
{
    #[Route('/technician/{checklistId?}', name: 'technician_dashboard', requirements: ['checklistId' => '\d+'])]
    public function index(?int $checklistId, Request $request, ChecklistsRepository $checklistsRepository): Response
    {
		if($checklistId){
			$selectedChecklist = $checklistsRepository->find($checklistId);
            $tasks = $selectedChecklist->getTaches();
		} else {
			$selectedChecklist=NULL;
			$tasks=NULL;
		}
		
		
        // Récupérer toutes les checklists
        $checklists = $checklistsRepository->findAll();

        // Créer le formulaire
		$form = $this->createFormBuilder()
            ->add('checklist', ChoiceType::class, [
                'choices' => $checklists,
                'choice_label' => 'titre',
				'choice_value' => 'id',
   	            'placeholder' => 'Choisir une checklist',
				'data' => $selectedChecklist, 
				'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Voir',
        ])
        ->getForm();

		// Afficher le formulaire dans le template Twig
		return $this->render('technician/index.html.twig', [
			'form' => $form->createView(),
			'tasks' => $tasks, // Passer les tâches au template Twig
		]);
    }
	
	#[Route('/technician/selection', name: 'technician_selection_checklist')]
    public function readChecklist(Request $request,ChecklistsRepository $checklistsRepository): Response
    {	
		// Récupérer toutes les checklists
        $checklists = $checklistsRepository->findAll();

        // Créer le formulaire
		$form = $this->createFormBuilder()
            ->add('checklist', ChoiceType::class, [
                'choices' => $checklists,
                'choice_label' => 'titre',
				'choice_value' => 'id',
   	            'placeholder' => 'Choisir une checklist',
				'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Voir',
            ])
            ->getForm();
		
		$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
			$checklist = $form->get('checklist')->getData();
			if($checklist){
				$checklistId = $checklist->getId();
			} else {
				$checklistId = null;
			}
		} else {
			$checklistId = null;
		}	
			
		return $this->redirectToRoute('technician_dashboard', ['checklistId' => $checklistId]);	
	}
	
	
	
}