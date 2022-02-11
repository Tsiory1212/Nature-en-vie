<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *      fields={"email"},
 *     message="Cet adresse mail est déjà utilisé par un utilisateur"
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\Length(
     *      min = 8, 
     *      minMessage="Votre mot de passe doit faire minimum 8 caractères"
     * )
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=FactureAbonnement::class, mappedBy="user")
     */
    private $factureAbonnements;

    /**
     * @ORM\OneToMany(targetEntity=FavoriteCart::class, mappedBy="user", orphanRemoval=true)
     */
    private $favoriteCarts;


    /**
     * @ORM\OneToOne(targetEntity=Delivry::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $delivry;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $orders;

    public function __construct()
    {
        $this->factureAbonnements = new ArrayCollection();
        $this->favoriteCarts = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
            $factureAbonnement->setUser($this);
        }

        return $this;
    }

    public function removeFactureAbonnement(FactureAbonnement $factureAbonnement): self
    {
        if ($this->factureAbonnements->removeElement($factureAbonnement)) {
            // set the owning side to null (unless already changed)
            if ($factureAbonnement->getUser() === $this) {
                $factureAbonnement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FavoriteCart[]
     */
    public function getFavoriteCarts(): Collection
    {
        return $this->favoriteCarts;
    }

    public function addFavoriteCart(FavoriteCart $favoriteCart): self
    {
        if (!$this->favoriteCarts->contains($favoriteCart)) {
            $this->favoriteCarts[] = $favoriteCart;
            $favoriteCart->setUser($this);
        }

        return $this;
    }

    public function removeFavoriteCart(FavoriteCart $favoriteCart): self
    {
        if ($this->favoriteCarts->removeElement($favoriteCart)) {
            // set the owning side to null (unless already changed)
            if ($favoriteCart->getUser() === $this) {
                $favoriteCart->setUser(null);
            }
        }

        return $this;
    }


    public function getDelivry(): ?Delivry
    {
        return $this->delivry;
    }

    public function setDelivry(Delivry $delivry): self
    {
        // set the owning side of the relation if necessary
        if ($delivry->getUser() !== $this) {
            $delivry->setUser($this);
        }

        $this->delivry = $delivry;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
