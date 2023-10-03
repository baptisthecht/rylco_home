<?php

namespace App\Entity;

use App\Repository\DesignSystemTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignSystemTypeRepository::class)]
class DesignSystemType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: DesignSystem::class)]
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
            $designSystem->setType($this);
        }

        return $this;
    }

    public function removeDesignSystem(DesignSystem $designSystem): self
    {
        if ($this->designSystems->removeElement($designSystem)) {
            // set the owning side to null (unless already changed)
            if ($designSystem->getType() === $this) {
                $designSystem->setType(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->getName();
    }
}
