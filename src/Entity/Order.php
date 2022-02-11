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
     * @ORM\Column(type="array")
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
     * @ORM\Column(type="string", length=50)
     */
    private $payer_id_paypal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payer_address_email_paypal;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $transaction_number_paypal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->status = 0;
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

    public function getPayerIdPaypal(): ?string
    {
        return $this->payer_id_paypal;
    }

    public function setPayerIdPaypal(string $payer_id_paypal): self
    {
        $this->payer_id_paypal = $payer_id_paypal;

        return $this;
    }

    public function getPayerEmailPaypal(): ?string
    {
        return $this->payer_address_email_paypal;
    }

    public function setPayerEmailPaypal(?string $payer_address_email_paypal): self
    {
        $this->payer_address_email_paypal = $payer_address_email_paypal;

        return $this;
    }

    public function getTransactionNumberPaypal(): ?string
    {
        return $this->transaction_number_paypal;
    }

    public function setTransactionNumberPaypal(string $transaction_number_paypal): self
    {
        $this->transaction_number_paypal = $transaction_number_paypal;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
