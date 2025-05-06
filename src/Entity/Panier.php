<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="panier")
 * @ORM\Entity(repositoryClass="App\Repository\PanierRepository")
 */
class Panier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ref", inversedBy="paniers")
     */
    private $statut;

    /**
     * @ORM\Column(name="dateCreation",type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(name="prixTotal",type="float", nullable=true)
     */
    private $prixTotal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="paniers")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="creerPar_id", referencedColumnName="id")
     * })
     */
    private $creerPar;

    /**
     * @ORM\Column(name="numPanier", type="string", length=255, nullable=true)
     */
    private $numPanier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="idPanier")
     */
    private $commandes;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sejour", inversedBy="paniers")
      * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idSejour_id", referencedColumnName="id")
     * })
     */
    private $idSejour;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierProduit", mappedBy="idPanier")
     */
    private $panierProduits;
    /**
     * @ORM\Column(name="Jeton",type="integer", nullable=true)
     */
    private $jeton;

    /**
     * @return mixed
     */
    public function getJeton()
    {
        return $this->jeton;
    }

    /**
     * @param mixed $jeton
     */
    public function setJeton($jeton): void
    {
        $this->jeton = $jeton;
    }


    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->panierProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?Ref
    {
        return $this->statut;
    }

    public function setStatut(?Ref $statut): self
    {
        $this->statut = $statut;

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

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(?float $prixTotal): self
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getCreerPar(): ?User
    {
        return $this->creerPar;
    }

    public function setCreerPar(?User $creerPar): self
    {
        $this->creerPar = $creerPar;

        return $this;
    }

    public function getNumPanier(): ?string
    {
        return $this->numPanier;
    }

    public function setNumPanier(?string $numPanier): self
    {
        $this->numPanier = $numPanier;

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
            $commande->setIdPanier($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getIdPanier() === $this) {
                $commande->setIdPanier(null);
            }
        }

        return $this;
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

    /**
     * @return Collection|PanierProduit[]
     */
    public function getPanierProduits(): Collection
    {
        return $this->panierProduits;
    }

    public function addPanierProduit(PanierProduit $panierProduit): self
    {
        if (!$this->panierProduits->contains($panierProduit)) {
            $this->panierProduits[] = $panierProduit;
            $panierProduit->setIdPanier($this);
        }

        return $this;
    }

    public function removePanierProduit(PanierProduit $panierProduit): self
    {
        if ($this->panierProduits->contains($panierProduit)) {
            $this->panierProduits->removeElement($panierProduit);
            // set the owning side to null (unless already changed)
            if ($panierProduit->getIdPanier() === $this) {
                $panierProduit->setIdPanier(null);
            }
        }

        return $this;
    }
}
