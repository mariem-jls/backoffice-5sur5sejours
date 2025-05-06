<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotonsumeriquesRepository")
 */
class Photonsumeriques
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sejour", inversedBy="photonsumeriques")
     */
    private $idSejour;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="photonsumeriques")
     */
    private $idUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SejourAttachment", inversedBy="photonsumeriques")
     */
    private $idSejourAttachement;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", inversedBy="photonsumeriques")
     */
    private $idProduit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSejour(): ?Sejour
    {
        return $this->idSejour;
    }

    public function setIdSejour(?Sejour $idSejour): self
    {
        $this->idSejour = $idSejour;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdSejourAttachement(): ?SejourAttachment
    {
        return $this->idSejourAttachement;
    }

    public function setIdSejourAttachement(?SejourAttachment $idSejourAttachement): self
    {
        $this->idSejourAttachement = $idSejourAttachement;

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

    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }
}
