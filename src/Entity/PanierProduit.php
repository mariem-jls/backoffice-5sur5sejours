<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="panierproduit")
 * @ORM\Entity(repositoryClass="App\Repository\PanierProduitRepository")
 */
class PanierProduit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", inversedBy="panierProduits")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idProduit_id", referencedColumnName="id")
     * })

     */
    private $idProduit;



    /**
     * @ORM\Column(name="prixTotal",type="integer", nullable=true)
     */
    private $prixTotal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Panier", inversedBy="panierProduits")
    * @ORM\JoinColumns
     *  @ORM\JoinColumn(name="idPanier_id", referencedColumnName="id")
     * })

     */
    private $idPanier;

    public function getId(): ?int
    {
        return $this->id;
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



    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(?float $prixTotal): self
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getIdPanier(): ?Panier
    {
        return $this->idPanier;
    }

    public function setIdPanier(?Panier $idPanier): self
    {
        $this->idPanier = $idPanier;

        return $this;
    }
}
