<?php

namespace App\Entity;

use App\Repository\AnneeHasClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeHasClasseRepository::class)]
class AnneeHasClasse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $scolarite = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    #[ORM\ManyToOne(inversedBy: 'anneeHasClasses')]
    private ?Annee $annee = null;

    #[ORM\ManyToOne(inversedBy: 'anneeHasClasses')]
    private ?Classe $classe = null;

    #[ORM\OneToMany(mappedBy: 'ahc', targetEntity: Scolarite::class)]
    private Collection $scolarites;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $createdUsername = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedUsername = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedUsername(): ?string
    {
        return $this->createdUsername;
    }

    public function setCreatedUsername(string $createdUsername): self
    {
        $this->createdUsername = $createdUsername;

        return $this;
    }

    public function getUpdatedUsername(): ?string
    {
        return $this->updatedUsername;
    }

    public function setUpdatedUsername(?string $updatedUsername): self
    {
        $this->updatedUsername = $updatedUsername;

        return $this;
    }

    public function __construct()
    {
        $this->scolarites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScolarite(): ?float
    {
        return $this->scolarite;
    }

    public function setScolarite(float $scolarite): self
    {
        $this->scolarite = $scolarite;

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

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getAnnee(): ?Annee
    {
        return $this->annee;
    }

    public function setAnnee(?Annee $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection<int, Scolarite>
     */
    public function getScolarites(): Collection
    {
        return $this->scolarites;
    }

    public function addScolarite(Scolarite $scolarite): self
    {
        if (!$this->scolarites->contains($scolarite)) {
            $this->scolarites->add($scolarite);
            $scolarite->setAhc($this);
        }

        return $this;
    }

    public function removeScolarite(Scolarite $scolarite): self
    {
        if ($this->scolarites->removeElement($scolarite)) {
            // set the owning side to null (unless already changed)
            if ($scolarite->getAhc() === $this) {
                $scolarite->setAhc(null);
            }
        }

        return $this;
    }
}
