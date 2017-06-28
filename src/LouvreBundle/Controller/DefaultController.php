<?php

namespace LouvreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LouvreBundle\Form\CommandeType;
use Symfony\Component\HttpFoundation\Request;
use LouvreBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\Response;
use LouvreBundle\Form\CommandeBilletType;
use LouvreBundle\Entity\Billet;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('LouvreBundle::infos.html.twig');
	}

	public function mentionsLegalesAction()
	{
		return $this->render('LouvreBundle::mentionsLegales.html.twig');
	}

	public function commandeAction(Request $request)
	{
		$commande = new Commande();

		$form = $this->createForm(CommandeType::class, $commande);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			// Controle date sélectionnée est correcte
			$commandeIsValid = $this->get('checkoutManager')->checkCommandeIsValid($commande);
			if (!$commandeIsValid) {
				return $this->redirectToRoute('louvre_commande');
			}

			// Mets en session la commande
			$session = $this->get('session');
			$session->set('commande', $commande);

			return $this->redirectToRoute('louvre_billets');
		}

		return $this->render('LouvreBundle::form.html.twig', array(
			'form' => $form->createView()
			));
	}

	public function billetsAction(Request $request)
	{	
		$commande = $this->getCommandeFromSession();
		if ($commande == null) {
			return $this->redirectToRoute('louvre_commande');
		}

		if (!$request->isMethod('POST'))
		{
			// On prépare le nouveau formulaire
			$commande->emptyBillets();
			for ($i = 0; $i < $commande->getNbrPersonnes(); $i++)
			{
				$commande->addBillet(new Billet());
			}
		}
    	
		$form = $this->createForm(CommandeBilletType::class, $commande);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$session = $this->get('session');
			$session->set('commande', $commande);
			return $this->redirectToRoute('louvre_checkout');
		}

		// On envoie sur la page de finalisation de la commande
		return $this->render('LouvreBundle::formFinal.html.twig', array(
			'form' => $form->createView()
			));

	}

	public function checkoutAction(Request $request)
	{
		$commande = $this->getCommandeFromSession();
		if ($commande == null) {
			return $this->redirectToRoute('louvre_commande');
		}

		foreach ($commande->getBillets() as $billet)
		{
			$billet->setCommande($commande);
		}

		// Calcule le prix de chaque billet en fonction de l'age
		$checkoutManager = $this->get('checkoutManager');
		$checkoutManager->setPricesForBilletsInCommand($commande);
		
		// Calcule le prix total de la commande
		$cumulPrice = $checkoutManager->getTotalPriceForCommand($commande);
		$commande->setTotal($cumulPrice);

		// Si le paiement a été fait
		if ($request->isMethod('POST'))
		{
			// Génère un booking code
			$commande->setBookingCode(strtoupper(uniqid()));

			// Débite la carte du client
			$token = $request->get('stripeToken');
			$checkoutManager->processPayment($token, $cumulPrice);

			// Enregistrement en bdd
			$em = $this->getDoctrine()->getManager();
			$em->persist($commande);
			$em->flush();

			// Vide la commande
			$this->get('session')->set('commande', null);
			return $this->redirectToRoute('louvre_homepage');
		}
		
		return $this->render('LouvreBundle::checkout.html.twig', array(
			'commande' => $commande,
			'total' => $cumulPrice,
			'stripe_public_key' => $this->getParameter('stripe_public_key')
			));
	}

	public function getCommandeFromSession()
	{
		$session = $this->get('session');
		$commande = $session->get('commande');

		return $commande;
	}
}
