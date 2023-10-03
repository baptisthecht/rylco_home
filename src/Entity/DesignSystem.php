<?php

namespace App\Entity;

use App\Repository\DesignSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignSystemRepository::class)]
class DesignSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: user::class, inversedBy: 'designSystems')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    #[ORM\ManyToOne(targetEntity: DesignSystemType::class, inversedBy: 'designSystems')]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\ManyToOne(targetEntity: DesignSystemState::class, inversedBy: 'designSystems')]
    #[ORM\JoinColumn(nullable: false)]
    private $state;

    #[ORM\OneToMany(mappedBy: 'DesignSystem', targetEntity: Component::class)]
    private $components;

    public function __construct()
    {
        $this->components = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?user
    {
        return $this->owner;
    }

    public function setOwner(?user $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getType(): ?designsystemtype
    {
        return $this->type;
    }

    public function setType(?designsystemtype $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?DesignSystemState
    {
        return $this->state;
    }

    public function setState(?DesignSystemState $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Component>
     */
    public function getComponents(): Collection
    {
        return $this->components;
    }

    public function addComponent(Component $component): self
    {
        if (!$this->components->contains($component)) {
            $this->components[] = $component;
            $component->setDesignSystem($this);
        }

        return $this;
    }

    public function removeComponent(Component $component): self
    {
        if ($this->components->removeElement($component)) {
            // set the owning side to null (unless already changed)
            if ($component->getDesignSystem() === $this) {
                $component->setDesignSystem(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->getName();
    }
}
