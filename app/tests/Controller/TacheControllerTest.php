<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ChecklistsRepository;
use App\Repository\TachesRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\UsersRepository;

class TacheControllerTest extends WebTestCase
{
   public function testIndexPage()
    {
        // Créer un client de test
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);

        // Faire une requête GET vers la page du manager
        $crawler = $client->request('GET', '/taches');

        // Vérifier que la réponse est réussie (code HTTP 200)
        $titreTache = $crawler->filter('#form_task_1 #taches_titre')->attr('value');
		$this->assertEquals('Nettoyage physique des ordinateurs', $titreTache);

    }

   public function testTache()
    {
        // Créer un client de test
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);

        // Faire une requête GET vers la page du manager
        $crawler = $client->request('GET', '/taches');

        $pageContent = $client->getResponse()->getContent();
		$newTaskForm = $crawler->filter('form#new_task')->form();
		$newTaskTitle = 'Nouvelle tâche ' . uniqid();
        // Remplir le formulaire de création de tâche
        $newTaskForm['taches[titre]'] = $newTaskTitle;
        $client->submit($newTaskForm);

        // Vérifier si la redirection vers la page de création a eu lieu
        $this->assertSame('/taches/creer', $client->getRequest()->getRequestUri());
		$crawler = $client->followRedirect();
		$this->assertSame('/taches', $client->getRequest()->getRequestUri());
		$bodyNode = $crawler->filter('body');
		$bodyContent = $bodyNode->html();

		// Vérifier que la nouvelle tâche est présente dans le contenu de la page
		//$this->assertContains('Nouvelle tâche', $bodyContent);
		$this->assertTrue(strpos($bodyContent, $newTaskTitle) !== false);
		$editForms = $crawler->filter('form.form-modification');
		// Sélectionner le dernier formulaire de modification

		$lastEdit = $editForms->last();
		$lastEditForm = $lastEdit->form();

		$changeTaskTitle = 'Nouvelle tâche ' . uniqid();

		$values = $lastEditForm->getPhpValues();
		$values['taches']['save'] = '';
		$values['taches']['titre'] = $changeTaskTitle;

		$client->request(
			$lastEditForm->getMethod(),
			$lastEditForm->getUri(),
			$values
		);
		
        // Vérifier si la redirection vers la page de création a eu lieu
        $this->assertMatchesRegularExpression('~^/taches/modifier/\d+$~', $client->getRequest()->getRequestUri());
		$crawler = $client->followRedirect();
		$this->assertSame('/taches', $client->getRequest()->getRequestUri());
		$bodyNode = $crawler->filter('body');
		$bodyContent = $bodyNode->html();		
		
		// Vérifier que le nouveau titre est présent dans le contenu de la page
		//$this->assertContains('Nouvelle tâche', $bodyContent);
		$this->assertTrue(strpos($bodyContent, $changeTaskTitle) !== false);
		$editForms = $crawler->filter('form.form-modification');
		// Sélectionner le dernier formulaire de modification

		$lastEdit = $editForms->last();
		$lastEditForm = $lastEdit->form();

		$values = $lastEditForm->getPhpValues();
		$values['taches']['delete'] = '';

		$deleteTaskTitle = $values['taches']['titre'];

		$client->request(
			$lastEditForm->getMethod(),
			$lastEditForm->getUri(),
			$values
		);
        // Vérifier si la redirection vers la page de création a eu lieu
        $this->assertMatchesRegularExpression('~^/taches/modifier/\d+$~', $client->getRequest()->getRequestUri());
		$crawler = $client->followRedirect();
		$this->assertSame('/taches', $client->getRequest()->getRequestUri());
		$bodyNode = $crawler->filter('body');
		$bodyContent = $bodyNode->html();		
		// Vérifier que le nouveau titre est absent dans le contenu de la page
		//$this->assertContains('Nouvelle tâche', $bodyContent);
		$this->assertTrue(strpos($bodyContent, $deleteTaskTitle) === false);

    }

}