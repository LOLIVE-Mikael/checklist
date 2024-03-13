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

class ManagerController extends AbstractController
{	

	
    #[Route('/manager/', name: 'manager_dashboard')]
    public function index(?int $checklistId, Request $request, ChecklistsRepository $checklistsRepository, TachesRepository $tachesRepository): Response
    {	
		// Afficher le formulaire dans le template Twig
		return $this->render('manager/index.html.twig');
    }
	
}
