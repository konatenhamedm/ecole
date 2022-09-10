<?php

namespace App\Entity;

use App\Repository\TesterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TesterRepository::class)]
class Tester
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Test::class, inversedBy: 'testers')]
    private Collection $tt;

    #[ORM\ManyToOne(inversedBy: 'yy')]
    private ?Test $champsT = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $leo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $goo = null;

    #[ORM\Column]
    private ?bool $b = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hh = null;

    public function __construct()
    {
        $this->tt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Test>
     */
    public function getTt(): Collection
    {
        return $this->tt;
    }

    public function addTt(Test $tt): self
    {
        if (!$this->tt->contains($tt)) {
            $this->tt->add($tt);
        }

        return $this;
    }

    public function removeTt(Test $tt): self
    {
        $this->tt->removeElement($tt);

        return $this;
    }

    public function getChampsT(): ?Test
    {
        return $this->champsT;
    }

    public function setChampsT(?Test $champsT): self
    {
        $this->champsT = $champsT;

        return $this;
    }

    public function getLeo(): ?\DateTimeInterface
    {
        return $this->leo;
    }

    public function setLeo(\DateTimeInterface $leo): self
    {
        $this->leo = $leo;

        return $this;
    }

    public function getGoo(): ?string
    {
        return $this->goo;
    }

    public function setGoo(string $goo): self
    {
        $this->goo = $goo;

        return $this;
    }

    public function isB(): ?bool
    {
        return $this->b;
    }

    public function setB(bool $b): self
    {
        $this->b = $b;

        return $this;
    }

    public function getHh(): ?string
    {
        return $this->hh;
    }

    public function setHh(?string $hh): self
    {
        $this->hh = $hh;

        return $this;
    }
}
