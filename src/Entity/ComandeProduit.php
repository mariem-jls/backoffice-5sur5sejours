<?php

namespace App\Entity;

use App\Entity\Produit;
use App\Entity\Commande;
use Doctrine\ORM\Mapping as ORM;

/**
 * ComandeProduit
 *
 * @ORM\Table(name="comande_produit", indexes={@ORM\Index(name="IdCommand_idx", columns={"id_comande"}), @ORM\Index(name="IdProduit_idx", columns={"id_produit"})})
 * @ORM\Entity(repositoryClass="App\Repository\ComandeProduitRepository")
 */
class ComandeProduit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantiter", type="integer", nullable=true)
     */
    private $quantiter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reversement", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $reversement;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \Commande
     *
     * @ORM\ManyToOne(targetEntity="Commande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_comande", referencedColumnName="id")
     * })
     */
    private $idComande;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_produit", referencedColumnName="id")
     * })
     */
    private $idProduit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pourcentage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiter(): ?int
    {
        return $this->quantiter;
    }

    public function setQuantiter(?int $quantiter): self
    {
        $this->quantiter = $quantiter;

        return $this;
    }

    public function getReversement(): ?string
    {
        return $this->reversement;
    }

    public function setReversement(?string $reversement): self
    {
        $this->reversement = $reversement;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdComande(): ?Commande
    {
        return $this->idComande;
    }

    public function setIdComande(?Commande $idComande): self
    {
        $this->idComande = $idComande;

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

    public function getPourcentage(): ?int
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?int $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }


}
