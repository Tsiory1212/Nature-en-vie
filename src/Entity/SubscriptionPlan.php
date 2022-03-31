<?php

namespace App\Entity;

use App\Repository\SubscriptionPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionPlanRepository::class)
 */
class SubscriptionPlan
{
    const INTERVAL_UNIT = [
        'day' => 'Jour',
        'week' => 'Semaine',
        'month' => 'Mois',
        'year' => 'An'
    ];


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $product_id_stripe;

    /**
     * NB : Pour utiliser des plans d'abonnement, on doit utiliser le Price de Stripe mais non pas Plan
     * @see  https://stripe.com/docs/billing/migration/migrating-prices
     * 
     * @ORM\Column(type="string", length=50)
     */
    private $price_id_stripe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detailed_description;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $interval_unit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trial_period_days;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="planId")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductIdStripe(): ?string
    {
        return $this->product_id_stripe;
    }

    public function setProductIdStripe(string $product_id_stripe): self
    {
        $this->product_id_stripe = $product_id_stripe;

        return $this;
    }

    public function getPriceIdStripe(): ?string
    {
        return $this->price_id_stripe;
    }

    public function setPriceIdStripe(string $price_id_stripe): self
    {
        $this->price_id_stripe = $price_id_stripe;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDetailedDescription(): ?string
    {
        return $this->detailed_description;
    }

    public function setDetailedDescription(?string $detailed_description): self
    {
        $this->detailed_description = $detailed_description;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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

    public function getTrialPeriodDays(): ?int
    {
        return $this->trial_period_days;
    }

    public function setTrialPeriodDays(?int $trial_period_days): self
    {
        $this->trial_period_days = $trial_period_days;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setPlanId($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getPlanId() === $this) {
                $order->setPlanId(null);
            }
        }

        return $this;
    }
}
