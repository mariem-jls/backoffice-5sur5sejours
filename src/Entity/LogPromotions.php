<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogPromotionsRepository")
 */
class LogPromotions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="logPromotions")
     */
    private $idCommande;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="logPromotions")
     */
    private $idClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promotions", inversedBy="logPromotions")
     */
    private $idPromotion;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="logPromo")
     */
    private $commandes;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdClient(): ?User
    {
        return $this->idClient;
    }

    public function setIdClient(?User $idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getIdPromotion(): ?Promotions
    {
        return $this->idPromotion;
    }

    public function setIdPromotion(?Promotions $idPromotion): self
    {
        $this->idPromotion = $idPromotion;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setLogPromo($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getLogPromo() === $this) {
                $commande->setLogPromo(null);
            }
        }

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
}
