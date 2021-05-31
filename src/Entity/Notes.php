<?php

namespace App\Entity;

use App\Repository\NotesRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotesRepository::class)
 */
class Notes
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
    private $matiere;

    /**
     * @ORM\Column(type="integer", length=255)
     * @Groups("eleve")
     */
    private $valeur;

    /**
     * @ORM\ManyToOne(targetEntity=Eleves::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eleve;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(string $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getEleve(): ?Eleves
    {
        return $this->eleve;
    }

    public function setEleve(?Eleves $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }
}
