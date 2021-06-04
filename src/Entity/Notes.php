<?php

namespace App\Entity;

use App\Repository\NotesRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=NotesRepository::class)
 * @OA\Schema()
 */
class Notes
{
    /**
     * @var $id integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("eleve")
     * @OA\Property(type="integer", nullable=false)
     */
    private $id;

    /**
     * @var $matiere string
     * @ORM\Column(type="string", length=255)
     * @Groups("eleve")
     * @OA\Property(type="string", nullable=false)
     */
    private $matiere;

    /**
     * @var $valeur integer
     * @ORM\Column(type="integer", length=255)
     * @Groups("eleve")
     * @OA\Property(type="integer", nullable=false)
     */
    private $valeur;

    /**
     * @ORM\ManyToOne(targetEntity=Eleves::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eleves;

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

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getEleves(): ?Eleves
    {
        return $this->eleves;
    }

    public function setEleves(?Eleves $eleves): self
    {
        $this->eleves = $eleves;

        return $this;
    }

}
