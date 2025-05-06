<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit", indexes={@ORM\Index(name="fk_produittype_idx", columns={"type"}), @ORM\Index(name="fk_user_idx", columns={"iduser"}), @ORM\Index(name="fk_Produitsjour_idx", columns={"idsjour"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
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
     * @var string|null
     *
     * @ORM\Column(name="labele", type="string", length=45, nullable=true)
     */
    private $labele;

    /**
     * @var int|null
     *
     * @ORM\Column(name="prix", type="integer", nullable=true)
     */
    private $prix;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Produitcol", type="string", length=45, nullable=true)
     */
    private $produitcol;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \Sejour
     *
     * @ORM\ManyToOne(targetEntity="Sejour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsjour", referencedColumnName="id")
     * })
     */
    private $idsjour;

    /**
     * @var \Typeproduit
     *
     * @ORM\ManyToOne(targetEntity="Typeproduit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduser", referencedColumnName="id")
     * })
     */
    private $iduser;
    
    
        /**
     * @var string|null
     *
     * @ORM\Column(name="statut", type="string", length=45, nullable=true)
     */
    private $statut;
    
     /**
     * @var int|null
     *
     * @ORM\Column(name="nbattach", type="integer", nullable=true)
     */
    private $nbattach;
    
     /**
     * @var string|null
     * @ORM\Column(name="pathpdf", type="text",nullable=true)
     */
    private $pathpdf;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeProduitConditionnement", inversedBy="produits")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idConditionnement_id", referencedColumnName="id")
     * })
     */
    private $idConditionnement;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierProduit", mappedBy="idProduit")
     */
    private $panierProduits;


      /**
     * @var int|null
     *
     * @ORM\Column(name="delated", type="integer", nullable=true)
     */
    private $delated;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="version", type="string", length=45, nullable=true)
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photonsumeriques", mappedBy="idProduit")
     */
    private $photonsumeriques;
    

    public function __construct()
    {
        $this->panierProduits = new ArrayCollection();
        $this->photonsumeriques = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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
    
    public function getLabele(): ?string
    {
        return $this->labele;
    }

    public function setLabele(?string $labele): self
    {
        $this->labele = $labele;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    
    public function getNbattach(): ?int
    {
        return $this->nbattach;
    }

    public function setNbattach(?int $nbattach): self
    {
        $this->nbattach = $nbattach;

        return $this;
    }




    public function getDelated(): ?int
    {
        return $this->delated;
    }

    public function setDelated(?int $delated): self
    {
        $this->delated = $delated;

        return $this;
    }
    public function getProduitcol(): ?string
    {
        return $this->produitcol;
    }

    public function setProduitcol(?string $produitcol): self
    {
        $this->produitcol = $produitcol;

        return $this;
    }

        public function getPathpdf(): ?string
    {
        return $this->pathpdf;
    }

    public function setPathpdf(?string $pathpdf): self
    {
        $this->pathpdf = $pathpdf;

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

    public function getIdsjour(): ?Sejour
    {
        return $this->idsjour;
    }

    public function setIdsjour(?Sejour $idsjour): self
    {
        $this->idsjour = $idsjour;

        return $this;
    }

    public function getType(): ?Typeproduit
    {
        return $this->type;
    }

    public function setType(?Typeproduit $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdConditionnement(): ?TypeProduitConditionnement
    {
        return $this->idConditionnement;
    }

    public function setIdConditionnement(?TypeProduitConditionnement $idConditionnement): self
    {
        $this->idConditionnement = $idConditionnement;

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
            $panierProduit->setIdProduit($this);
        }

        return $this;
    }

    public function removePanierProduit(PanierProduit $panierProduit): self
    {
        if ($this->panierProduits->contains($panierProduit)) {
            $this->panierProduits->removeElement($panierProduit);
            // set the owning side to null (unless already changed)
            if ($panierProduit->getIdProduit() === $this) {
                $panierProduit->setIdProduit(null);
            }
        }

        return $this;
    }
    
    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection|Photonsumeriques[]
     */
    public function getPhotonsumeriques(): Collection
    {
        return $this->photonsumeriques;
    }

    public function addPhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if (!$this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques[] = $photonsumerique;
            $photonsumerique->setIdProduit($this);
        }

        return $this;
    }

    public function removePhotonsumerique(Photonsumeriques $photonsumerique): self
    {
        if ($this->photonsumeriques->contains($photonsumerique)) {
            $this->photonsumeriques->removeElement($photonsumerique);
            // set the owning side to null (unless already changed)
            if ($photonsumerique->getIdProduit() === $this) {
                $photonsumerique->setIdProduit(null);
            }
        }

        return $this;
    }
}
