<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaireEtiquetteRepository")
 */
class CommentaireEtiquette
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $statut;
    /**
     * @ORM\Column(name="typecommentaire" ,type="string", length=45, nullable=true)
     */
    private $typecommentaire;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentaireEtiquettes")
     */
    private $createur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etiquette", inversedBy="commentaireEtiquettes")
     */
    private $etiquette;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreateur(): ?User
    {
        return $this->createur;
    }

    public function setCreateur(?User $createur): self
    {
        $this->createur = $createur;

        return $this;
    }

    public function getEtiquette(): ?Etiquette
    {
        return $this->etiquette;
    }

    public function setEtiquette(?Etiquette $etiquette): self
    {
        $this->etiquette = $etiquette;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut): void
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getTypecommentaire()
    {
        return $this->typecommentaire;
    }

    /**
     * @param mixed $typecommentaire
     */
    public function setTypecommentaire($typecommentaire): void
    {
        $this->typecommentaire = $typecommentaire;
    }




}
