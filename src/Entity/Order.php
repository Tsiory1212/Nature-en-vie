<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    const PAYMENT_TYPE = [
        0 => 'paiement_unique',
        1 => 'paiement_recurrent',
        2 => 'souscription_plan'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $cart = [];

    /**
     * @ORM\Column(type="float")
     */
    private $total_price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;


    /**
     * Permet de savoir si la commande est livrée ou non
     * 
     * @ORM\Column(type="boolean")
     */
    private $status_delivry;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;


    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\OneToOne(targetEntity=PauseDelivry::class, inversedBy="order_paused", cascade={"persist", "remove"})
     */
    private $pause_delivry;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $payment_type;

    /**
     * @ORM\Column(type="array")
     */
    private $stripe_data = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $SubscriptionPlanDatas = [];



    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->status_delivry = 0;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getCart(): ?array
    {
        return $this->cart;
    }

    public function setCart(array $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): self
    {
        $this->total_price = $total_price;

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


    public function getStatusDelivry(): ?bool
    {
        return $this->status_delivry;
    }

    public function setStatusDelivry(bool $status_delivry): self
    {
        $this->status_delivry = $status_delivry;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }



    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPauseDelivry(): ?PauseDelivry
    {
        return $this->pause_delivry;
    }

    public function setPauseDelivry(?PauseDelivry $pause_delivry): self
    {
        $this->pause_delivry = $pause_delivry;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->payment_type;
    }

    public function setPaymentType(string $payment_type): self
    {
        $this->payment_type = $payment_type;

        return $this;
    }

    public function getStripeData(): ?array
    {
        return $this->stripe_data;
    }

    public function setStripeData(array $stripe_data): self
    {
        $this->stripe_data = $stripe_data;

        return $this;
    }

    /**
     * Pemet de récupérer le type de paiment 
     */
    public function getOrderPaymentType(): string
    {
        return self::PAYMENT_TYPE[$this->payment_type];
    }

    public function getSubscriptionPlanDatas(): ?array
    {
        return $this->SubscriptionPlanDatas;
    }

    public function setSubscriptionPlanDatas(?array $SubscriptionPlanDatas): self
    {
        $this->SubscriptionPlanDatas = $SubscriptionPlanDatas;

        return $this;
    }

}
