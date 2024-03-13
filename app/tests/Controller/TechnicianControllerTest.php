<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UsersRepository;

class TechnicianControllerTest extends WebTestCase
{
    public function testIndexPage()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneBy(['login' => 'Technicien1']);

        $client->loginUser($testUser);

        // Request the index page
        $crawler = $client->request('GET', '/technician');

        // Assert that the page loads successfully
        $this->assertResponseIsSuccessful();

        // Assert that the form exists on the page
        $this->assertSelectorExists('form');

        // Submit the form with valid data
        $client->submitForm('Voir', [
            'form[checklist]' => '1',
        ]);

        // Assert that the redirection to the same page occurred after form submission
		$this->assertResponseRedirects('/technician/1');
		
        // Follow the redirect
        $client->followRedirect();

        // Assert that the redirected page loads successfully
        $this->assertResponseIsSuccessful();

        // Assert that the tasks for the selected checklist are displayed
        $this->assertSelectorTextContains('h2', 'Tasks for the selected checklist:');
    }
}