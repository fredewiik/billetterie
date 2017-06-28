<?php

namespace tests\LouvreBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use LouvreBundle\Manager\CheckoutManager;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use LouvreBundle\Entity\Commande;

class CheckoutManagerTest extends TestCase
{
	public function testSetPriceTest()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
		$session = new Session(new MockArraySessionStorage());
		$checkoutManager = new CheckoutManager('key', $em, $session, 0, 8, 12, 16, 10);

		$result = $checkoutManager->getPriceForAge(3);

		$this->assertEquals(0, $result);
	}

	public function testTuesday()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
		$session = new Session(new MockArraySessionStorage());
		$checkoutManager = new CheckoutManager('key', $em, $session, 0, 8, 12, 16, 10);

		$commande = new Commande();
		$commande->setDate(new \DateTime('2017-12-25')); // Noël
		$commande->setTicketType('Journée');
		$commande->setNbrPersonnes(2);
		$commande->setEmail('1988.fred@gmail.com');

		$result = $checkoutManager->checkCommandeIsValid($commande);

		$this->assertFalse($result);
	}
}
