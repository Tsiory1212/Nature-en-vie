<?php

namespace App\Entity;

use App\Repository\FactureAbonnementRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureAbonnementRepository::class)
 */
class FactureAbonnement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * NB: planSubscriptionId in Paypal
     * 
     * @ORM\Column(type="string", length=30)
     */
    private $subscriptionId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="factureAbonnements")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=CartSubscription::class, inversedBy="factureAbonnements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cartSubscription;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $subscription_end;

    /**
     * @ORM\OneToOne(targetEntity=PauseLivraison::class, mappedBy="facture_abonnement", fetch="EAGER")
     */
    private $pause_livraison;

    public function __construct()
    {
        $this->created_at = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCartSubscription(): ?CartSubscription
    {
        return $this->cartSubscription;
    }

    public function setCartSubscription(?CartSubscription $cartSubscription): self
    {
        $this->cartSubscription = $cartSubscription;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getSubscriptionEnd(): ?\DateTimeInterface
    {
        return $this->subscription_end;
    }

    public function setSubscriptionEnd(\DateTimeInterface $subscription_end): self
    {
        $this->subscription_end = $subscription_end;

        return $this;
    }

    public function getPauseLivraison(): ?PauseLivraison
    {
        return $this->pause_livraison;
    }

    public function setPauseLivraison(?PauseLivraison $pause_livraison): self
    {
        $this->pause_livraison = $pause_livraison;

        return $this;
    }

    public function isPaused()
    {
        if ($this->pause_livraison) {
            return true;
        }
        return false;
    }
}
