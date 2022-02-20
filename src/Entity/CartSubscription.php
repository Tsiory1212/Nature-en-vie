<?php

namespace App\Entity;

use App\Repository\CartSubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CartSubscriptionRepository::class)
 * @Vich\Uploadable()
 * @UniqueEntity(
 *      fields={"nameSubscriptionPlan"},
 *      message="{{ value }} existe déjà"
 * )
 */
class CartSubscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $idProductPlanPaypal;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $nameSubscriptionPlan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionSubscriptionPlan;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $priceSubscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $durationMonthSubscription;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $idSubscriptionPlanPaypal;

    /**
     * @ORM\OneToMany(targetEntity=FactureAbonnement::class, mappedBy="cartSubscription")
     */
    private $factureAbonnements;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $detailedDescription;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $interval_unit;

    /**
     * @ORM\Column(type="boolean" )
     */
    private $active;

    public function __construct()
    {
        $this->factureAbonnements = new ArrayCollection();
        $this->active = 1;
    }


    public function getId(): ?int
    {
        return $this->id;
    
    }

    public function getIdProductPlanPaypal(): ?string
    {
        return $this->idProductPlanPaypal;
    }

    public function setIdProductPlanPaypal($idProductPlanPaypal): self
    {
        $this->idProductPlanPaypal = $idProductPlanPaypal;

        return $this;
    }

    public function getNameSubscriptionPlan(): ?string
    {
        return $this->nameSubscriptionPlan;
    }

    public function setNameSubscriptionPlan(string $nameSubscriptionPlan): self
    {
        $this->nameSubscriptionPlan = $nameSubscriptionPlan;

        return $this;
    }

    public function getDescriptionSubscriptionPlan(): ?string
    {
        return $this->descriptionSubscriptionPlan;
    }

    public function setDescriptionSubscriptionPlan(string $descriptionSubscriptionPlan): self
    {
        $this->descriptionSubscriptionPlan = $descriptionSubscriptionPlan;

        return $this;
    }

    public function getPriceSubscription(): ?string
    {
        return $this->priceSubscription;
    }

    public function setPriceSubscription(string $priceSubscription): self
    {
        $this->priceSubscription = $priceSubscription;

        return $this;
    }

    public function getDurationMonthSubscription(): ?int
    {
        return $this->durationMonthSubscription;
    }

    public function setDurationMonthSubscription(int $durationMonthSubscription): self
    {
        $this->durationMonthSubscription = $durationMonthSubscription;

        return $this;
    }

    public function getIdSubscriptionPlanPaypal(): ?string
    {
        return $this->idSubscriptionPlanPaypal;
    }

    public function setIdSubscriptionPlanPaypal(string $idSubscriptionPlanPaypal): self
    {
        $this->idSubscriptionPlanPaypal = $idSubscriptionPlanPaypal;

        return $this;
    }

    /**
     * @return Collection|FactureAbonnement[]
     */
    public function getFactureAbonnements(): Collection
    {
        return $this->factureAbonnements;
    }

    public function addFactureAbonnement(FactureAbonnement $factureAbonnement): self
    {
        if (!$this->factureAbonnements->contains($factureAbonnement)) {
            $this->factureAbonnements[] = $factureAbonnement;
            $factureAbonnement->setCartSubscription($this);
        }

        return $this;
    }

    public function removeFactureAbonnement(FactureAbonnement $factureAbonnement): self
    {
        if ($this->factureAbonnements->removeElement($factureAbonnement)) {
            // set the owning side to null (unless already changed)
            if ($factureAbonnement->getCartSubscription() === $this) {
                $factureAbonnement->setCartSubscription(null);
            }
        }

        return $this;
    }

    public function getDetailedDescription(): ?string
    {
        return $this->detailedDescription;
    }

    public function setDetailedDescription(?string $detailedDescription): self
    {
        $this->detailedDescription = $detailedDescription;

        return $this;
    }

    public function getIntervalUnit(): ?string
    {
        return $this->interval_unit;
    }

    public function setIntervalUnit(string $interval_unit): self
    {
        $this->interval_unit = $interval_unit;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    
}
