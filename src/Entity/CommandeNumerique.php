<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeNumeriqueRepository")
 */
class CommandeNumerique
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $linkdownload;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateTelechargement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="commandeNumeriques")
     */
    private $idCommande;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $envoi;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLinkdownload(): ?string
    {
        return $this->linkdownload;
    }

    public function setLinkdownload(?string $linkdownload): self
    {
        $this->linkdownload = $linkdownload;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateTelechargement(): ?\DateTimeInterface
    {
        return $this->dateTelechargement;
    }

    public function setDateTelechargement(?\DateTimeInterface $dateTelechargement): self
    {
        $this->dateTelechargement = $dateTelechargement;

        return $this;
    }

    public function getIdCommande(): ?Commande
    {
        return $this->idCommande;
    }

    public function setIdCommande(?Commande $idCommande): self
    {
        $this->idCommande = $idCommande;

        return $this;
    }

    public function getEnvoi(): ?int
    {
        return $this->envoi;
    }

    public function setEnvoi(?int $envoi): self
    {
        $this->envoi = $envoi;

        return $this;
    }
}
