<?php

namespace App\Entity;

use App\Repository\IconsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IconsRepository::class)
 */
#[ORM\Entity(repositoryClass: IconsRepository::class)]
class Icons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(length: 255)]
    private ?string $code =null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @ORM\OneToMany(targetEntity=Groupe::class, mappedBy="icon")
     */
    private $groupes;

    /**
     * @ORM\OneToMany(targetEntity=Module::class, mappedBy="icon")
     */
    private $modules;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(length: 255)]
    private ?string $active = null;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->modules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setIcon($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getIcon() === $this) {
                $groupe->setIcon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->setIcon($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getIcon() === $this) {
                $module->setIcon(null);
            }
        }

        return $this;
    }

    public function getActive(): ?string
    {
        return $this->active;
    }

    public function setActive(string $active): self
    {
        $this->active = $active;

        return $this;
    }
}
