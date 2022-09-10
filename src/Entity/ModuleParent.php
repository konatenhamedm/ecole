<?php

namespace App\Entity;

use App\Repository\ModuleParentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModuleParentRepository::class)
 */
#[ORM\Entity(repositoryClass: ModuleParentRepository::class)]
class ModuleParent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    /**
     * @ORM\OneToMany(targetEntity=Module::class, mappedBy="parent")
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Module::class)]
    private Collection $modules;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column]
    private ?int $ordre = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column]
    private ?int $active = null;



    public function __construct()
    {
        $this->modules = new ArrayCollection();
    }

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
            $module->setParent($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getParent() === $this) {
                $module->setParent(null);
            }
        }

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }
    public function  getListElement(){
        $var =['titre'=>'titre','ordre'=>'ordre'];
        return $var;
    }
    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }


}
