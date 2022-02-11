<?php

namespace App\Entity;

use App\Repository\PauseLivraisonRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PauseLivraisonRepository::class)
 */
class PauseLivraison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today", message="La date de debut doit être ultérieure à la date d'aujourd'hui !")
     */
    private $start_date;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(propertyPath="start_date", message="La date de fin doit être plus éloignée que la date de début !")
     * 
     */
    private $end_date;

    /**
     * @ORM\OneToOne(targetEntity=FactureAbonnement::class, inversedBy="pause_livraison")
     * @ORM\JoinColumn(nullable=false)
     */
    private $facture_abonnement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * Get the value of facture_abonnement
     */ 
    public function getFactureAbonnement()
    {
        return $this->facture_abonnement;
    }

    /**
     * Set the value of facture_abonnement
     *
     * @return  self
     */ 
    public function setFactureAbonnement($facture_abonnement)
    {
        $this->facture_abonnement = $facture_abonnement;

        return $this;
    }
}
