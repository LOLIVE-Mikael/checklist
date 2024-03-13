<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TachesController extends AbstractController
{
    #[Route('/taches', name: 'app_taches')]
    public function index(): Response
    {
        return $this->render('taches/index.html.twig', [
            'controller_name' => 'TachesController',
        ]);
    }
}
