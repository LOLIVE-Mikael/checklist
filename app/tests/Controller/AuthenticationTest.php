<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    public function testAdmin()
    {
        $client = static::createClient();
        $this->assertNotNull($client);
        // Submit the login form with valid credentials
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['login'] = 'Admin';
        $form['password'] = 'admin';
        $client->submit($form);

        // Check if the user is redirected to the homepage
		$this->assertResponseRedirects('/');
        $this->assertResponseStatusCodeSame(302);

        // Test access to /technician
        $client->request('GET', '/technician');
        $this->assertResponseIsSuccessful();

        // Test access to /manager
        $client->request('GET', '/manager');
        $this->assertResponseIsSuccessful();
    }
/*
    public function testManagerRedirect()
    {
		$client = static::createClient();

        $client->request('GET', '/login');

        // Submit the login form with valid credentials
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['login'] = 'Manager';
        $form['password'] = 'manager';
        $client->submit($form);

        $this->assertResponseRedirects('/manager');
        $this->assertResponseStatusCodeSame(302);

        // Test access to /technician
        $client->request('GET', '/technician');
        $this->assertResponseStatusCodeSame(403); // Access Denied

        // Test access to /manager
        $client->request('GET', '/manager');
        $this->assertResponseIsSuccessful();
    }

    public function testTechnicianRedirect()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        // Submit the login form with valid credentials
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();
        $form['login'] = 'Technicien1';
        $form['password'] = 'technicien1';
        $client->submit($form);

        $this->assertResponseRedirects('/technician');
        $this->assertResponseStatusCodeSame(302);

        // Test access to /technician
        $client->request('GET', '/technician');
        $this->assertResponseIsSuccessful();

        // Test access to /manager
        $client->request('GET', '/manager');
        $this->assertResponseStatusCodeSame(403); // Access Denied
    }*/
} 