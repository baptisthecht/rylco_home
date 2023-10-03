<?php

namespace App\Entity;

use App\Repository\DesignSystemStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignSystemStateRepository::class)]
class DesignSystemState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'state', targetEntity: DesignSystem::class)]
    private $designSystems;

    public function __construct()
    {
        $this->designSystems = new ArrayCollection();
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

    /**
     * @return Collection<int, DesignSystem>
     */
    public function getDesignSystems(): Collection
    {
        return $this->designSystems;
    }

    public function addDesignSystem(DesignSystem $designSystem): self
    {
        if (!$this->designSystems->contains($designSystem)) {
            $this->designSystems[] = $designSystem;
            $designSystem->setState($this);
        }

        return $this;
    }

    public function removeDesignSystem(DesignSystem $designSystem): self
    {
        if ($this->designSystems->removeElement($designSystem)) {
            // set the owning side to null (unless already changed)
            if ($designSystem->getState() === $this) {
                $designSystem->setState(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->getName();
    }
}
