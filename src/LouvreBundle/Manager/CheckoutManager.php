<?php

namespace LouvreBundle\Manager;

use LouvreBundle\Entity\Commande;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CheckoutManager
{

	private $secretKey;
	private $em;
	private $session;

	private $tarifEnfant0_4;
	private $tarifEnfant4_12;
	private $tarifNormal;
	private $tarifSenior;
	private $tarifReduit;

	public function __construct($secretKey, $em, $session, $tarifEnfant0_4, $tarifEnfant4_12, $tarifNormal, $tarifSenior, $tarifReduit)
	{
		$this->secretKey = $secretKey;
		$this->em = $em;
		$this->session = $session;
		$this->tarifEnfant0_4 = $tarifEnfant0_4;
		$this->tarifEnfant4_12 = $tarifEnfant4_12;
		$this->tarifNormal = $tarifNormal;
		$this->tarifSenior = $tarifSenior;
		$this->tarifReduit = $tarifReduit;
	}

	public function checkCommandeIsValid($commande)
	{
		$date = $commande->getDate();

		// Si date est un mardi
		$day = $date->format('D');
		if ($day == "Tue" || $day == "Sun") {
			$this->session->getFlashBag()->add('warning', 'Le musée est fermé tous les mardi et les dimanche, veuillez sélectionner un autre jour');
			return false;
		}
		
		// Si date passée
		$today = new \DateTime('now');
		if ($date->format('Y-m-d') < $today->format('Y-m-d')){
			$this->session->getFlashBag()->add('warning', 'Vous ne pouvez pas prendre de ticket pour une date passée');
			return false;
		}

		// Si date est un jour férié
		$closedDays = ['01-05', '01-11', '25-12'];
		$day = $date->format('d-m');
		if (in_array($day, $closedDays)) {
			$this->session->getFlashBag()->add('warning', 'Le musée est fermé à cette date, veuillez sélectionner un autre jour');
			return false;
		}

		// Si billet journée alors qu'il est plus de 14h
		$hour = $today->format('H');
		$today = $today->format('d/m/y');
		$dateCommande = $date->format('d/m/y');
		if ($today == $dateCommande && $commande->getTicketType() == 'Journée' && (int)$hour >= 14) {
			$this->session->getFlashBag()->add('warning', 'Les billets journée ne sont plus disponible après 14h');
			return false;
		}
		
		// Si il y a plus de 1000 billets pour ce jour
		$count = $this->em->getRepository('LouvreBundle:Commande')->countBilletsForDate($date);
		if ($count >= 1000) {
			$this->session->getFlashBag()->add('warning', 'Nous sommes désolés mais il n\'y a plus de tickets disponibles pour le ' .$date);
			return false;
		}
		
		return true;
	}


	public function processPayment($token, $total)
	{
		\Stripe\Stripe::setApiKey($this->secretKey);

		// Charge the user's card:
		$charge = \Stripe\Charge::create(array(
 		 "amount" => $total * 100,
 		 "currency" => "eur",
 		 "description" => "Example charge",
 		 "source" => $token,
		));
	}

	public function setPricesForBilletsInCommand(Commande $commande)
	{
		foreach($commande->getBillets() as $billet)
		{
			if($billet->getHasDiscount())
			{
				$billet->setPrice($this->tarifReduit);

			} else {
				$dateCmde = $commande->getDate();
				$ageClient = $dateCmde->diff($billet->getBirthDate())->y;
				$price = $this->getPriceForAge($ageClient);
				$billet->setPrice($price);
			}
		}
	}

	public function getTotalPriceForCommand(Commande $commande)
	{
		$cumulPrice = 0;

		foreach($commande->getBillets() as $billet)
		{
			$cumulPrice += $billet->getPrice();
		}		

		return $cumulPrice;
	}

	public function getPriceForAge($age)
	{
		if($age > 60) {
			return $this->tarifSenior;
		} elseif($age > 12) {
			return $this->tarifNormal;
		} elseif ($age > 4) {
			return $this->tarifEnfant4_12;
		} else {
			return $this->tarifEnfant0_4;
		}
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$commande = $args->getEntity();
        $this->sendEmail($commande);
	}
}

