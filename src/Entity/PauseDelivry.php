<?php

namespace App\Entity;

use App\Repository\PauseDelivryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PauseDelivryRepository::class)
 */
class PauseDelivry
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
     */
    private $end_date;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, mappedBy="pause_delivry", cascade={"persist", "remove"})
     */
    private $order_paused;

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

    public function getOrderPaused(): ?Order
    {
        return $this->order_paused;
    }

    public function setOrderPaused(?Order $order_paused): self
    {
        // unset the owning side of the relation if necessary
        if ($order_paused === null && $this->order_paused !== null) {
            $this->order_paused->setPauseDelivry(null);
        }

        // set the owning side of the relation if necessary
        if ($order_paused !== null && $order_paused->getPauseDelivry() !== $this) {
            $order_paused->setPauseDelivry($this);
        }

        $this->order_paused = $order_paused;

        return $this;
    }
}
