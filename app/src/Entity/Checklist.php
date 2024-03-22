<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Repository\ChecklistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Controller\Api\ApiChecklistController;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\Model\Parameter;

#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
#[ApiResource(
	operations: [
	    new Delete(
			name: 'remove_task', 
			uriTemplate: 'checklists/{checklistid}/tasks/{tacheid}', 
			read: false,
			controller: ApiChecklistController::class,
			openapi: new Model\Operation(
				summary: 'retirer une tache d\'une checklist',
				description: 'retire la tache avec l\'id "tacheid" de la checklist avec l\'id "checklistid"',
				parameters: [
					new Parameter('checklistid', 'path',
						'Identifiant de la checklist',
						true,false,false,
						['type' => 'integer']),
					new Parameter('tacheid','path',
						'Identifiant de la tache',
						true,false,false,
						['type' => 'integer']),
				]
			)
		),
	]
)]
class Checklist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
	#[Groups(['read'])]
    #[ApiProperty(
        openapiContext: [
            'type' => 'integer'
			]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToMany(targetEntity: Tache::class, inversedBy: 'Checklists')]
    private Collection $taches;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
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
     * @return Collection<int, Taches>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): static
    {
	
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
        }

        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        $this->taches->removeElement($tache);

        return $this;
    }
}
