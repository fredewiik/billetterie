<?php

namespace tests\LouvreBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;
use LouvreBundle\Entity\Commande;

class DefaultControllerTest extends WebTestCase
{
	public function testIndex()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/');

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	public function testBilletsPage()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/billets');
		$crawler = $client->followRedirect();

		$this->assertContains('Achetez vos billets', $crawler->text());
	}

	public function testCommandeForm()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/billets');
		$crawler = $client->followRedirect();

		$form = $crawler->selectButton('submit')->form(array(
			'louvrebundle_commande[date]' => '28/06/2017',
			'louvrebundle_commande[ticketType]' => 'Journée',
			'louvrebundle_commande[nbrPersonnes]' => 2,
			'louvrebundle_commande[email]' => '1988.fredgmail.com', // email non valide
		));

		$crawler = $client->submit($form);

		$this->assertTrue($crawler->filter('.alert-danger')->count() == 1);
	}

}
