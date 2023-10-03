<?php

namespace App\Entity;

use App\Repository\ComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComponentRepository::class)]
class Component
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: ComponentState::class, inversedBy: 'components')]
    #[ORM\JoinColumn(nullable: false)]
    private $state;

    #[ORM\ManyToOne(targetEntity: ComponentType::class, inversedBy: 'components')]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'components')]
    private $owner;

    #[ORM\ManyToOne(targetEntity: DesignSystem::class, inversedBy: 'components')]
    #[ORM\JoinColumn(nullable: false)]
    private $DesignSystem;

    #[ORM\Column(type: 'string', length: 100000000, nullable: true)]
    private $code;

    #[ORM\Column(type: 'float', nullable: true)]
    private $price;

    #[ORM\OneToMany(mappedBy: 'component', targetEntity: Activity::class)]
    private $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getState(): ?ComponentState
    {
        return $this->state;
    }

    public function setState(?ComponentState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getType(): ?ComponentType
    {
        return $this->type;
    }

    public function setType(?ComponentType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDesignSystem(): ?DesignSystem
    {
        return $this->DesignSystem;
    }

    public function setDesignSystem(?DesignSystem $DesignSystem): self
    {
        $this->DesignSystem = $DesignSystem;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setComponent($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getComponent() === $this) {
                $activity->setComponent(null);
            }
        }

        return $this;
    }
}
