<?php

namespace LouvreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LouvreBundle\Entity\Billet;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="LouvreBundle\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Range(min=1, minMessage="Veuillez sélectionner au minimum 1")
     * @Assert\NotBlank(message="Veuillez sélectionner le nombre de visiteurs")
     */
    private $nbrPersonnes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime(message="Veuillez saisir une date valide")
     * @Assert\NotBlank(message="Veuillez saisir une date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="ticketType", type="string", length=255)
     * @Assert\NotBlank(message="Veuillez sélectionner un type de billet")
     */
    private $ticketType;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(message="Veuillez entrer une adresse email valide")
     * @Assert\NotBlank(message="Ce champs ne doit pas être vide")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="bookingCode", type="string", length=255)
     */
    private $bookingCode;

    /**
     * @ORM\OneToMany(targetEntity="LouvreBundle\Entity\Billet", cascade={"persist"}, mappedBy="commande")
     * @Assert\Valid()
     */
    private $billets;

    private $total;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->billets = new ArrayCollection();
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Commande
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set bookingCode
     *
     * @param string $bookingCode
     *
     * @return Commande
     */
    public function setBookingCode($bookingCode)
    {
        $this->bookingCode = $bookingCode;

        return $this;
    }

    /**
     * Get bookingCode
     *
     * @return string
     */
    public function getBookingCode()
    {
        return $this->bookingCode;
    }

    /**
     * Add billet
     *
     * @param \LouvreBundle\Entity\Billet $billet
     *
     * @return Commande
     */
    public function addBillet(Billet $billet)
    {
        $this->billets[] = $billet;

        return $this;
    }

    /**
     * Remove billet
     *
     * @param \LouvreBundle\Entity\Billet $billet
     */
    public function removeBillet(Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }
    
    public function emptyBillets()
    {
        $this->billets = array();
    }

    public function count()
    {
        return $this->getBillets()->count();
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set ticketType
     *
     * @param string $ticketType
     *
     * @return Commande
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    public function setNbrPersonnes($nbr)
    {
        return $this->nbrPersonnes = $nbr;
    }

    public function getNbrPersonnes()
    {
        return $this->nbrPersonnes;
    }

    public function setBillets($billets)
    {
        foreach ($billets as $billet) 
        {
            $this->addBillet($billet);
        }
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

}
