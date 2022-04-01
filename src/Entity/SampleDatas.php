<?php

namespace App\Entity;

use App\Repository\SampleDatasRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SampleDatasRepository::class)
 * @Vich\Uploadable()
 * 
 * @UniqueEntity(
 *      fields={"referenceId"},
 *      message="{{ value }} existe déjà"
 * )
 */
class SampleDatas
{
    const GAMME = [
        0 => 'Bio',
        1 => 'Demeter'
    ];
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(type="float", message="chest.invalid")
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detail;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @var File|null
     * @Assert\Image(
     *      maxSize = "2M",
     *      mimeTypes = {"image/png", "image/jpeg", "image/jpg"},
     * )
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     */
    private $imageFile;


    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products", fetch="EAGER")
     */
    private $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gamme;

    /**
     * @ORM\ManyToOne(targetEntity=Classement::class, inversedBy="products")
     */
    private $classement;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $referenceId;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $volume;

    /**
     * @ORM\Column(type="boolean")
     */
    private $availability;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $quantity_unit;

    public function __construct()
    {
        $this->updated_at = new \DateTime();
        $this->quantity = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
        $this->updated_at = new \DateTime();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

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


    /**
     * Get mimeTypes="image/jpeg"
     *
     * @return  File|null
     */ 
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     *
     * @return  self
     */ 
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new \DateTime();
        }
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getGammeType(): string
    {
        return self::GAMME[$this->gamme];
    }


    public function getGamme(): ?int
    {
        return $this->gamme;
    }

    public function setGamme(?int $gamme): self
    {
        $this->gamme = $gamme;

        return $this;
    }

    public function getClassement(): ?Classement
    {
        return $this->classement;
    }

    public function setClassement(?Classement $classement): self
    {
        $this->classement = $classement;

        return $this;
    }
    
    public function getSlug(): ?string  
    {
        return (new Slugify())->slugify($this->name);
    }

    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    public function setReferenceId(string $referenceId): self
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(bool $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getQuantityUnit(): ?string
    {
        return $this->quantity_unit;
    }

    public function setQuantityUnit(string $quantity_unit): self
    {
        $this->quantity_unit = $quantity_unit;

        return $this;
    }

}
