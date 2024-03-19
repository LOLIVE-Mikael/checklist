<?php

namespace App\Controller\Ajax;

use App\Entity\Tache;
use App\Form\TacheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class AjaxTacheController extends AbstractController
{
	
    #[Route('/ajax/tache/add', name: 'ajax_tache_add')]
    public function addTask(EntityManagerInterface $entityManager, Request $request): Response
    {
		//création de la nouvelle tâche
		$data = json_decode($request->getContent(), true);
		$titre = $data['titre'];
		
		$task = new Tache();
		$task->setTitre($titre);
			
		$entityManager->persist($task);
		$entityManager->flush();

		//création du formulaire
		$form = $this->createForm(TacheType::class, $task)				
			->add('update', SubmitType::class, ['label' => 'Modifier'])
			->add('delete', SubmitType::class, ['label' => 'Supprimer']);
		
		return $this->render('taches/formtask.html.twig', [
            'form' => $form,
			'taskId' => $task->getId(),
        ]);
	}
}
