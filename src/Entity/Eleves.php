<?php

namespace App\Entity;

use App\Repository\ElevesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ElevesRepository::class)
 */
class Eleves
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("eleve")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("eleve")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("eleve")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("eleve")
     */
    private $dateDeNaissance;

    /**
     * @var $notes ArrayCollection
     * @ORM\OneToMany(targetEntity=Notes::class, mappedBy="eleves", orphanRemoval=true)
     * @Groups("eleve")
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
