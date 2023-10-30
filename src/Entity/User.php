<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{

    #[Vich\UploadableField(mapping: 'user', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'float')]
    private $balance;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Component::class)]
    private $components;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: DesignSystem::class)]
    private $designSystems;

    #[ORM\Column(type: 'string', length: 10000, nullable: true)]
    private $about;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Activity::class)]
    private $sells;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Activity::class)]
    private $buys;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Deposit::class)]
    private Collection $deposits;

    #[ORM\Column(nullable: true)]
    private ?bool $isPro = null;


    public function __construct()
    {
        $this->components = new ArrayCollection();
        $this->designSystems = new ArrayCollection();
        $this->sells = new ArrayCollection();
        $this->buys = new ArrayCollection();
        $this->deposits = new ArrayCollection();
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

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

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
            $component->setOwner($this);
        }

        return $this;
    }

    public function removeComponent(Component $component): self
    {
        if ($this->components->removeElement($component)) {
            // set the owning side to null (unless already changed)
            if ($component->getOwner() === $this) {
                $component->setOwner(null);
            }
        }

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
            $designSystem->setOwner($this);
        }

        return $this;
    }

    public function removeDesignSystem(DesignSystem $designSystem): self
    {
        if ($this->designSystems->removeElement($designSystem)) {
            // set the owning side to null (unless already changed)
            if ($designSystem->getOwner() === $this) {
                $designSystem->setOwner(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getSells(): Collection
    {
        return $this->sells;
    }

    public function addSell(Activity $sell): self
    {
        if (!$this->sells->contains($sell)) {
            $this->sells[] = $sell;
            $sell->setSeller($this);
        }

        return $this;
    }

    public function removeSell(Activity $sell): self
    {
        if ($this->sells->removeElement($sell)) {
            // set the owning side to null (unless already changed)
            if ($sell->getSeller() === $this) {
                $sell->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getBuys(): Collection
    {
        return $this->buys;
    }

    public function addBuy(Activity $buy): self
    {
        if (!$this->buys->contains($buy)) {
            $this->buys[] = $buy;
            $buy->setBuyer($this);
        }

        return $this;
    }

    public function removeBuy(Activity $buy): self
    {
        if ($this->buys->removeElement($buy)) {
            // set the owning side to null (unless already changed)
            if ($buy->getBuyer() === $this) {
                $buy->setBuyer(null);
            }
        }

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function __serialize()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password
        ];
    }

    public function __unserialize($serialized)
    {
        $this->id = $serialized['id'];
        $this->email = $serialized['email'];
        $this->password = $serialized['password'];
        return $this;
    }


    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized);
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Deposit>
     */
    public function getDeposits(): Collection
    {
        return $this->deposits;
    }

    public function addDeposit(Deposit $deposit): static
    {
        if (!$this->deposits->contains($deposit)) {
            $this->deposits->add($deposit);
            $deposit->setCustomer($this);
        }

        return $this;
    }

    public function removeDeposit(Deposit $deposit): static
    {
        if ($this->deposits->removeElement($deposit)) {
            // set the owning side to null (unless already changed)
            if ($deposit->getCustomer() === $this) {
                $deposit->setCustomer(null);
            }
        }

        return $this;
    }

    public function isIsPro(): ?bool
    {
        return $this->isPro;
    }

    public function setIsPro(?bool $isPro): static
    {
        $this->isPro = $isPro;

        return $this;
    }

    public function __toString(): string
    {
        return $this-> firstname . ' ' .$this->lastname;
    }

}
