<?php

namespace LouvreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LouvreBundle\Entity\Commande;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="LouvreBundle\Repository\BilletRepository")
 */
class Billet
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Veuillez entrer votre nom")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="LouvreBundle\Entity\Commande", inversedBy="billets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     * @Assert\NotBlank(message="Veuillez entrer votre prénom")
     */
    private $firstName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date")
     * @Assert\DateTime()
     */
    private $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\Country()
     */
    private $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="hasDiscount", type="boolean")
     */
    private $hasDiscount;

    /**
     * @ORM\Column(name="price", type="integer")
    */
    private $price;


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
     * Set name
     *
     * @param string $name
     *
     * @return Billet
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Billet
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Billet
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Billet
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set hasDiscount
     *
     * @param boolean $hasDiscount
     *
     * @return Billet
     */
    public function setHasDiscount($hasDiscount)
    {
        $this->hasDiscount = $hasDiscount;

        return $this;
    }

    /**
     * Get hasDiscount
     *
     * @return bool
     */
    public function getHasDiscount()
    {
        return $this->hasDiscount;
    }

    /**
     * Set commande
     *
     * @param \LouvreBundle\Entity\Commande $commande
     *
     * @return Billet
     */
    public function setCommande(Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \LouvreBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Billet
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }
}
