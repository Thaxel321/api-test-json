<?php

namespace App\Entity;

use App\Repository\NotesRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=NotesRepository::class)
 */
class Notes
{
    /**
     * @var $id integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Assert\Type("integer")
     * @Groups({"readAllEleve", "readNote"})
     * @OA\Property(type="integer", nullable=false)
     */
    private $id;

    /**
     * @var $matiere string
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Groups({"readAllEleve", "createNote", "readNote"})
     * @OA\Property(type="string", nullable=false)
     */
    private $matiere;

    /**
     * @var $valeur integer
     * @ORM\Column(type="integer", length=255)
     * @Assert\Type("integer")
     * @Groups({"readAllEleve", "createNote", "readNote"})
     * @OA\Property(type="integer", nullable=false, minimum="0", maximum="20")
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
