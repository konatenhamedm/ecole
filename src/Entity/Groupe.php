<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GroupeRepository::class)
 */
#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("groupe:read")
     */
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(length: 255)]
    private ?string $lien = null;


    #[ORM\ManyToOne(inversedBy: 'groupes')]
    private ?Module $module = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column]
    private ?int $ordre = null;


    #[ORM\ManyToOne(inversedBy: 'groupes',cascade : ["persist"] )]
    private ?Icons $icon = null;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getIcon(): ?Icons
    {
        return $this->icon;
    }

    public function setIcon(?Icons $icon): self
    {
        $this->icon = $icon;

        return $this;
    }


}
