<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $rol = [];

    #[ORM\Column]
    private ?int $active = null;

    #[ORM\ManyToMany(targetEntity: Tester::class, mappedBy: 'tt')]
    private Collection $testers;

    #[ORM\OneToMany(mappedBy: 'champsT', targetEntity: Tester::class)]
    private Collection $yy;

    public function __construct()
    {
        $this->test2s = new ArrayCollection();
        $this->testers = new ArrayCollection();
        $this->yy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRol(): array
    {
        return $this->rol;
    }

    public function setRol(array $rol): self
    {
        $this->rol = $rol;

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

    /**
     * @return Collection<int, Tester>
     */
    public function getTesters(): Collection
    {
        return $this->testers;
    }

    public function addTester(Tester $tester): self
    {
        if (!$this->testers->contains($tester)) {
            $this->testers->add($tester);
            $tester->addTt($this);
        }

        return $this;
    }

    public function removeTester(Tester $tester): self
    {
        if ($this->testers->removeElement($tester)) {
            $tester->removeTt($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tester>
     */
    public function getYy(): Collection
    {
        return $this->yy;
    }

    public function addYy(Tester $yy): self
    {
        if (!$this->yy->contains($yy)) {
            $this->yy->add($yy);
            $yy->setChampsT($this);
        }

        return $this;
    }

    public function removeYy(Tester $yy): self
    {
        if ($this->yy->removeElement($yy)) {
            // set the owning side to null (unless already changed)
            if ($yy->getChampsT() === $this) {
                $yy->setChampsT(null);
            }
        }

        return $this;
    }

}
