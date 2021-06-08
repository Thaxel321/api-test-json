<?php

namespace App\Entity;

use App\Repository\ElevesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=ElevesRepository::class)
 */
class Eleves
{
    /**
     * @var $id integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Assert\Type("integer")
     * @Groups({"readAllEleve", "readEleve"})
     * @OA\Property(type="integer", nullable=false)
     */
    private $id;

    /**
     * @var $nom string
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Groups({"readAllEleve", "readEleve", "createEleve"})
     * @OA\Property(type="string", nullable=false)
     */
    private $nom;

    /**
     * @var $prenom string
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Groups({"readAllEleve", "readEleve", "createEleve"})
     * @OA\Property(type="string", nullable=false)
     */
    private $prenom;

    /**
     * @var $dateDeNaissance  string
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Groups({"readAllEleve", "readEleve", "createEleve"})
     * @OA\Property(type="string",format="date", nullable=false)
     */
    private $dateDeNaissance;

    /**
     * @var $notes ArrayCollection
     * @ORM\OneToMany(targetEntity=Notes::class, mappedBy="eleves", orphanRemoval=true)
     * @Groups("readAllEleve")
     */
    private $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateDeNaissance(): ?string
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(string $dateDeNaissance): self
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * @return Collection|Notes[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setEleves($this);
        }

        return $this;
    }

    public function getAverageNote(): float
    {
        $sum = 0;
        /** @var Notes $note */
        foreach ($this->notes as $note){
            $sum += $note->getValeur();
        }
        return round(($sum / count($this->notes)), 2);
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEleves() === $this) {
                $note->setEleves(null);
            }
        }

        return $this;
    }
}
