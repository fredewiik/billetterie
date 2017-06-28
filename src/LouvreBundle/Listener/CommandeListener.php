<?php

namespace LouvreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LouvreBundle\Manager\EmailManager;
use LouvreBundle\Entity\Commande;

class CommandeListener
{
	private $recipient;
	private $templating;
	private $mailer;
	private $session;

	public function __construct($recipient, $templating, $mailer, $session)
	{
		$this->recipient = $recipient;
		$this->templating = $templating;
		$this->mailer = $mailer;
		$this->session = $session;
	}

	public function postPersist(LifecycleEventArgs $args)
    {
        $commande = $args->getEntity();

        if($commande instanceof Commande)
        {
	        $this->sendEmail($commande);
	    }
	}

	public function sendEmail($commande)
	{
		$this->session->getFlashBag()->add('validate', 'Vos billets ont bien été commandé, vous allez recevoir un email de confirmation');

		$message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject("Vos billets au Louvre pour le " .$commande->getDate()->format('d/m/Y'))
                ->setFrom($this->recipient)
                ->setTo($commande->getEmail())
                ->setBody(
                	$this->templating->render('confirmationEmail.html.twig', [
                		'total' => $commande->getTotal(),
                		'commande' => $commande
                		])
                	);

        $this->mailer->send($message);
	}
}