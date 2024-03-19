<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ChecklistsRepository;
use App\Repository\TachesRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\UsersRepository;

class ChecklistControllerTest extends WebTestCase
{

   public function testIndexPage()
    {
        // Créer un client de test
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);

        // Faire une requête GET vers la page du manager
        $crawler = $client->request('GET', '/checklist');

        // Vérifier que la réponse est réussie (code HTTP 200)
        $this->assertResponseIsSuccessful();

        // Vérifier que le sélecteur pour le formulaire de checklist existe sur la page
        $this->assertSelectorExists('select[name="form[checklist]"]');
    }

    public function testReadNoChecklist()
    {
		$client = static::createClient();

        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/checklist');
		$form = $crawler->filter('#form-select-checklist')->form();
        $client->submit($form);
		$this->assertResponseRedirects('/checklist');
		$crawler = $client->followRedirect();		
        $this->assertResponseIsSuccessful();
	    $this->assertSelectorExists('select[name="form[checklist]"]');
		$selectedValue = $crawler->filter('select[name="form[checklist]"]')->attr('value');
				
		$this->assertEquals('', $selectedValue);
		
    }
	
	public function testDisplayTasksForSelectedChecklist()
    {
		$client = static::createClient();
		
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);
		
        $crawler = $client->request('GET', '/checklist');
		// Soumission du formulaire avec une checklist sélectionnée
		$form = $crawler->filter('#form-select-checklist')->form();
        $client->submitForm('Voir', [
            'form[checklist]' => '1',
        ]);
	    // Vérification de la redirection
		$this->assertResponseRedirects('/checklist/1');
		$crawler = $client->followRedirect();		

		// Vérification de la réussite de la réponse
        $this->assertResponseIsSuccessful();
		
		// Vérification de la checklist sélectionnée		
		$selectedValue = $crawler->filter('select[name="form[checklist]"] option[selected]')->attr('value');
		$this->assertEquals('1', $selectedValue);

        // Vérification de la liste des tâches
		$taskList = $crawler->filter('#task-list li');
		//récupération des taches affichées
		$taskListTexts = $taskList->each(function ($node) {
			    $text = $node->text();
				// Supprimer le texte "dissocier" du bouton
				$textWithoutButton = str_replace('Dissocier', '', $text);
				return $textWithoutButton;
		});
		$taskListTexts = array_map('trim', $taskListTexts);
        // Vérification des éléments de la liste des tâches
		$this->assertNotContains('Défragmentation du disque dur', $taskListTexts);
		$this->assertNotContains('Analyse des pare-feu', $taskListTexts);
		$this->assertNotContains('Nettoyer les fichiers temporaires', $taskListTexts);
		$this->assertContains('Nettoyage physique des ordinateurs', $taskListTexts);
		$this->assertContains('Mettre à jour SAP', $taskListTexts);
		$this->assertContains('Analyse antivirus', $taskListTexts);


		// Vérification de l'existence du formulaire des tâches 
        $form = $crawler->filter('#form-add-task')->form();
		$this->assertNotEmpty($form);
				
		// Vérification de la présence des tâches dans la liste déroulante		
		$taskDropdown = $crawler->filter('select[name="form[task]"]');
		$taskDropdownValues = $taskDropdown->filter('option')->extract(['_text']);
		
		//vérification que les tâches non rattachées à la checklist sont présentes dans la liste déroulante
		$this->assertContains('Défragmentation du disque dur', $taskDropdownValues);
		$this->assertContains('Analyse des pare-feu', $taskDropdownValues);
		$this->assertContains('Nettoyer les fichiers temporaires', $taskDropdownValues);
		$this->assertNotContains('Nettoyage physique des ordinateurs', $taskDropdownValues);
		$this->assertNotContains('Mettre à jour SAP', $taskDropdownValues);
		$this->assertNotContains('Analyse antivirus', $taskDropdownValues);
    }

	public function testAddTaskFromChecklist()
    {
        $client = static::createClient();
		
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);
		
        $crawler = $client->request('GET', '/checklist/1');
		$form = $crawler->filter('#form-add-task')->form();
        $client->submitForm('Ajouter', [
            'form[task]' => '4',
        ]);
	    // Vérification de la redirection
		$this->assertResponseRedirects('/checklist/1');
		$crawler = $client->followRedirect();

        // Vérification que la tache nouvellement ajouté est bien affichée dans la liste des tâches
		$taskList = $crawler->filter('#task-list li');
		//récupération des taches affichées
		$taskListTexts = $taskList->each(function ($node) {
			    $text = $node->text();
				// Supprimer le texte "dissocier" du bouton
				$textWithoutButton = str_replace('Dissocier', '', $text);
				return $textWithoutButton;
		});
		$taskListTexts = array_map('trim', $taskListTexts);
        // Vérification des éléments de la liste des tâches
		$this->assertContains('Défragmentation du disque dur', $taskListTexts);

		// Vérification de l'absence de la tâches d'ajout des taches		
		$taskDropdown = $crawler->filter('select[name="form[task]"]');
		$taskDropdownValues = $taskDropdown->filter('option')->extract(['_text']);

		
		//vérification que les tâches non rattachées à la checklist sont présentes dans la liste déroulante
		$this->assertNotContains('Défragmentation du disque dur', $taskDropdownValues);
		
		//la tache sera supprimé dans un autre test
    }

	public function testRemoveTaskFromChecklist()
    {
        $client = static::createClient();
		
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);
		
        $crawler = $client->request('GET', '/checklist/1');
		$form = $crawler->filter('#form-task-4')->form();		
			
        $client->submit($form);
	    // Vérification de la redirection
		$this->assertResponseRedirects('/checklist/1');
		$crawler = $client->followRedirect();

        // Vérification que la tache nouvellement retirée est bien absente de la liste des tâches
		$taskList = $crawler->filter('#task-list li');
		//récupération des taches affichées
		$taskListTexts = $taskList->each(function ($node) {
			    $text = $node->text();
				// Supprimer le texte "dissocier" du bouton
				$textWithoutButton = str_replace('Dissocier', '', $text);
				return $textWithoutButton;
		});
		$taskListTexts = array_map('trim', $taskListTexts);
        // Vérification des éléments de la liste des tâches
		$this->assertNotContains('Défragmentation du disque dur', $taskListTexts);

		// Vérification de l'absence de la tâches d'ajout des taches		
		$taskDropdown = $crawler->filter('select[name="form[task]"]');
		$taskDropdownValues = $taskDropdown->filter('option')->extract(['_text']);

		//vérification que les tâches non rattachées à la checklist sont présentes dans la liste déroulante
		$this->assertContains('Défragmentation du disque dur', $taskDropdownValues);
		
    }

    public function testAddNewTask()
    { 
		// Créer un titre aléatoire pour la nouvelle tâche
		$newTaskTitle = 'Nouvelle tâche ' . uniqid();
		
		$client = static::createClient();

        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Manager']);

        $client->loginUser($testUser);

		$crawler = $client->request('GET', '/checklist/1');
		$form = $crawler->filter('#form-add-task')->form();
		$client->submitForm('Ajouter', [
			'form[newTask]' => $newTaskTitle,
		]);

		$crawler = $client->followRedirect();	
		// Vérification que la tache nouvellement créé est bien affichée dans la liste des tâches
		$taskList = $crawler->filter('#task-list li');
		//récupération des taches affichées
		$taskListTexts = $taskList->each(function ($node) {
			$text = $node->text();
			// Supprimer le texte "dissocier" du bouton
			$textWithoutButton = str_replace('Dissocier', '', $text);
			return $textWithoutButton;
		});
		$taskListTexts = array_map('trim', $taskListTexts);
		// Vérification des éléments de la liste des tâches
		$this->assertContains($newTaskTitle, $taskListTexts);

		// Retire la tache de la checklist (pas utile mais plus propre)
		$taskToRemoveLi = $crawler->filter("#task-list li:contains('$newTaskTitle')")->first();

		// Cibler le bouton "Dissocier" à l'intérieur du <li>
		$form = $taskToRemoveLi->selectButton('Dissocier')->form();
		$client->submit($form);
		$crawler = $client->followRedirect();
	
    }
}
