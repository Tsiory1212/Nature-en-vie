<?php

namespace App\Entity;

use App\Repository\DelivryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DelivryRepository::class)
 */
class Delivry
{
    const TYPE = [
        0 => 'A domicile',
        1 => 'Point relais',
        2 => 'Livraison de panier en entreprise'
    ];

    const TIME_SLOT = [
        0 => '6h-7h',
        1 => '7h-8h',
        2 => '8h-9h',
        3 => '9h-10h',
        4 => '10h-11h',
        5 => '11h-12h',
        6 => '12h-13h',
        7 => '13h-14h',
        8 => '14h-15h',
        9 => '15h-16h',
        10 => '16h-17h',
        11 => '17h-18h',
        12 => '18h-19h',
        13 => '19h-20h'
    ];

    const DAY_SLOT = [
        0 => 'Lundi',
        1 => 'Mardi',
        2 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Samedi',
        6 => 'Dimanche'
    ];


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $time_slot;

    /**
     * @ORM\Column(type="integer")
     */
    private $day_slot;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="delivry", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat_position;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng_position;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $postal_code;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTimeSlot(): ?int
    {
        return $this->time_slot;
    }

    public function setTimeSlot(int $time_slot): self
    {
        $this->time_slot = $time_slot;

        return $this;
    }

    public function getDaySlot(): ?int
    {
        return $this->day_slot;
    }

    public function setDaySlot(int $day_slot): self
    {
        $this->day_slot = $day_slot;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

 
    public function getDelivryType(): string
    {
        return self::TYPE[$this->type];
    }

    public function getDelivryTimeSlot(): string
    {
        return self::TIME_SLOT[$this->time_slot];
    }

    public function getDelivryDaySlot(): string
    {
        return self::DAY_SLOT[$this->day_slot];
    }

    public function getLatPosition(): ?float
    {
        return $this->lat_position;
    }

    public function setLatPosition(?float $lat_position): self
    {
        $this->lat_position = $lat_position;

        return $this;
    }

    public function getLngPosition(): ?float
    {
        return $this->lng_position;
    }

    public function setLngPosition(?float $lng_position): self
    {
        $this->lng_position = $lng_position;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }


}
