<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtiquetteRepository")
 */
class Etiquette
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="etiquettes")
     */
    private $support;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="etiquetteRapporteur")
     */
    private $rapporteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentaireEtiquette", mappedBy="etiquette")
     */
    private $commentaireEtiquettes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttachementEtiquette", mappedBy="idEtiquette")
     */
    private $attachementEtiquettes;

    public function __construct()
    {
        $this->commentaireEtiquettes = new ArrayCollection();
        $this->attachementEtiquettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSupport(): ?User
    {
        return $this->support;
    }

    public function setSupport(?User $support): self
    {
        $this->support = $support;

        return $this;
    }

    public function getRapporteur(): ?User
    {
        return $this->rapporteur;
    }

    public function setRapporteur(?User $rapporteur): self
    {
        $this->rapporteur = $rapporteur;

        return $this;
    }

    /**
     * @return Collection|CommentaireEtiquette[]
     */
    public function getCommentaireEtiquettes(): Collection
    {
        return $this->commentaireEtiquettes;
    }

    public function addCommentaireEtiquette(CommentaireEtiquette $commentaireEtiquette): self
    {
        if (!$this->commentaireEtiquettes->contains($commentaireEtiquette)) {
            $this->commentaireEtiquettes[] = $commentaireEtiquette;
            $commentaireEtiquette->setEtiquette($this);
        }

        return $this;
    }

    public function removeCommentaireEtiquette(CommentaireEtiquette $commentaireEtiquette): self
    {
        if ($this->commentaireEtiquettes->contains($commentaireEtiquette)) {
            $this->commentaireEtiquettes->removeElement($commentaireEtiquette);
            // set the owning side to null (unless already changed)
            if ($commentaireEtiquette->getEtiquette() === $this) {
                $commentaireEtiquette->setEtiquette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttachementEtiquette[]
     */
    public function getAttachementEtiquettes(): Collection
    {
        return $this->attachementEtiquettes;
    }

    public function addAttachementEtiquette(AttachementEtiquette $attachementEtiquette): self
    {
        if (!$this->attachementEtiquettes->contains($attachementEtiquette)) {
            $this->attachementEtiquettes[] = $attachementEtiquette;
            $attachementEtiquette->setIdEtiquette($this);
        }

        return $this;
    }

    public function removeAttachementEtiquette(AttachementEtiquette $attachementEtiquette): self
    {
        if ($this->attachementEtiquettes->contains($attachementEtiquette)) {
            $this->attachementEtiquettes->removeElement($attachementEtiquette);
            // set the owning side to null (unless already changed)
            if ($attachementEtiquette->getIdEtiquette() === $this) {
                $attachementEtiquette->setIdEtiquette(null);
            }
        }

        return $this;
    }
}
