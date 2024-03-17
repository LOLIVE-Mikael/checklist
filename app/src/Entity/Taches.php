<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TachesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TachesRepository::class)]


class Taches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['minimal'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['minimal'])]
    private ?string $titre = null;

    #[ORM\ManyToMany(targetEntity: Checklists::class, mappedBy: 'taches')]
    private Collection $checklists;

    public function __construct()
    {
        $this->checklists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

	public function setId(int $id): void
	{
		$this->id = $id;
	}

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection<int, Checklists>
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklists $checklist): static
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists->add($checklist);
            $checklist->addTach($this);
        }

        return $this;
    }

    public function removeChecklist(Checklists $checklist): static
    {
        if ($this->checklists->removeElement($checklist)) {
            $checklist->removeTach($this);
        }

        return $this;
    }
}
